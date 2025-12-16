<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreditStoreRequest;
use App\Http\Requests\CreditUpdateRequest;
use App\Http\Resources\CreditCollection;
use App\Http\Resources\CreditResource;
use App\Mail\AccepteCredit;
use App\Mail\DemandeCredit;
use App\Mail\RefuserCredit;
use App\Models\Cotisation;
use App\Models\Credit;
use App\Models\Membre;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Mime\Email;

use function PHPSTORM_META\type;

class CreditController extends Controller
{



    public function approuveCredit($id)
    {
        // Get Credit ID et update statut to approuve et send email to member
        $credit = Credit::findOrFail($id);
        $credit->update([
            'statut' => 'approuve',
            'date_approbation' => now(),
        ]);

        try {
            //code...
            Mail::to($credit->membre->email)
                ->cc(EMAIL_USER)
                ->send(new AccepteCredit($credit->load('membre')));
        } catch (\Throwable $th) {
            //throw $th;
        }

        return sendResponse($credit, 'Credit approuve successfully.');
    }
    public function refuserCredit($id)
    {
        // Get Credit ID et update statut to refuser et send email to member
        $credit = Credit::findOrFail($id);
        $credit->update([
            'statut' => 'rejete',
            'date_approbation' => now(),
        ]);

        try {
            //code...
            Mail::to($credit->membre->email)
                ->cc(EMAIL_COPIES)
                ->send(new RefuserCredit($credit->load('membre')));
        } catch (\Throwable $th) {
            //throw $th;
        }
        return sendResponse($credit, 'Credit refuser successfully.');
    }
    public function demandeCredit(Request $request)
    {

        $Email_admin = User::where('role', 'admin')->orWhere('role', 'gestionnaire')->pluck('email')->toArray();

        $request->validate([
            'montant_demande' => 'required|numeric',
            'taux_interet' => 'required|numeric',
            'duree_mois' => 'required|numeric',
            'montant_total_rembourser' => 'required|numeric',
            'montant_mensualite' => 'required|numeric',
        ]);



        try {
            $membre = Membre::where('user_id', auth()->user()->id)->first();
        } catch (\Exception $e) {
            return sendError($e->getMessage());
        }
        // ✅ Check business rules before creating credit
        $hasIrregularCotisations = Cotisation::where('membre_id', $membre->id)
            ->whereIn('statut', ['en_attente', 'en_retard'])
            ->exists();

        $hasUnpaidCredits = Credit::where('membre_id', $membre->id)
            ->where('montant_restant', '!=', 0)
            ->exists();

        if ($hasIrregularCotisations || $hasUnpaidCredits) {
            return sendError(
                'Vous ne pouvez pas demander un crédit tant que vous avez des cotisations irrégulières ou des crédits impayés.',
                Response::HTTP_FORBIDDEN
            );
        }

        try {
            DB::beginTransaction();
            //code...
            $credit = Credit::create([
            'montant_demande' => $request->montant_demande,
            'taux_interet' => $request->taux_interet,
            'duree_mois' => $request->duree_mois,
            'montant_total_rembourser' => $request->montant_total_rembourser,
            'montant_mensualite' => $request->montant_mensualite,
            'membre_id' => $membre->id,
            'user_id' => auth()->user()->id,
            'statut' => 'en_attente',
            'date_demande' => now(),
            'motif' => $request->motif,
            'montant_accorde' => 0,
        ]);

        Notification::create([
             'type' => 'credit',
             'title' => 'Nouvelle demande de credit',
             'message' => 'Une nouvelle demande de credit a ete effectuee par '.$membre->nom.' '.$membre->prenom . ' pour un montant de ' . $request->montant_demande . ' BIF',
             'time' => now(),
             'read' => false,
             'user_id' => auth()->user()->id,
        ]);
        DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return sendError($th->getMessage());
        }
        try {
            // Envoie de l'email a l'admin
            Mail::to($Email_admin)
                ->cc(auth()->user()->email)
                ->send(new DemandeCredit($credit->load('membre')));
        } catch (\Exception $e) {
            return sendError($e->getMessage());
        }
        return sendResponse($credit, 'Credit created successfully.');
    }
    public function mesCredits()
    {
        try {
            $membre = Membre::where('user_id', auth()->user()->id)->first()->id;
            $credits = Credit::where('membre_id', $membre)->latest()->paginate();
        } catch (\Exception $e) {
            return sendError($e->getMessage());
        }
        return sendResponse($credits, 'Credits retrieved successfully.');
    }


    public function store(CreditStoreRequest $request)
    {
        // ✅ Check business rules before creating credit
        $hasIrregularCotisations = Cotisation::where('membre_id', $request->membre_id)
            ->whereIn('statut', ['en_attente', 'en_retard'])
            ->exists();

        $hasUnpaidCredits = Credit::where('membre_id', $request->membre_id)
            ->where('montant_restant', '!=', 0)
            ->exists();

        if ($hasIrregularCotisations || $hasUnpaidCredits) {
            return sendError(
                'Vous ne pouvez pas demander un crédit tant que vous avez des cotisations irrégulières ou des crédits impayés.',
                Response::HTTP_FORBIDDEN
            );
        }
        $credit = Credit::create(array_merge($request->validated(), [
            'user_id' => $request->user()->id ?? 1,
        ]));

        return new CreditResource($credit);
    }

    public function index(Request $request)
    {
        $params = $request->all();

        $inputSearch = $params['search'] ?? null;


        $query = Credit::query();


        // 🔎 Recherche : par nom ou prénom du membre, motif, ID
        if ($inputSearch) {
            $search = $inputSearch;

            $query->where(function ($q) use ($search) {
                $q->whereHas('membre', function ($m) use ($search) {
                    $m->where('nom', 'like', "%$search%")
                        ->orWhere('prenom', 'like', "%$search%");
                })
                    ->orWhere('motif', 'like', "%$search%")
                    ->orWhere('id', $search);
            });
        }

        // 📌 Filtre statut
        if (!empty($params['statut'])) {
            $query->where('statut', $params['statut']);
        }

        // 📅 Filtre date_demande (start / end)
        if (!empty($params['date_demande_start'])) {
            $query->whereDate('date_demande', '>=', $params['date_demande_start']);
        }

        if (!empty($params['date_demande_end'])) {
            $query->whereDate('date_demande', '<=', $params['date_demande_end']);
        }

        // 📅 Filtre date_fin précise
        if (!empty($params['date_fin'])) {
            $query->whereDate('date_fin', $params['date_fin']);
        }

        // 🔽 Tri dynamique
        $sortField = $params['sort_field'] ?? 'created_at';
        $sortOrder = $params['sort_order'] ?? 'desc';

        $query->orderBy($sortField, $sortOrder);

        // 📄 Pagination dynamique
        $perPage = $params['per_page'] ?? 15;
        $credits = $query->paginate($perPage);

        return sendResponse($credits, 'Credits retrieved successfully.');
    }

    public function show(Request $request, Credit $credit)
    {
        return sendResponse($credit->load("membre"), 'Credit retrieved successfully.');
    }

    public function update(CreditUpdateRequest $request, Credit $credit)
    {
        $credit->update($request->validated());

        return new CreditResource($credit);
    }

    public function destroy(Request $request, Credit $credit)
    {
        $credit->delete();
        return response()->noContent();
    }
}
