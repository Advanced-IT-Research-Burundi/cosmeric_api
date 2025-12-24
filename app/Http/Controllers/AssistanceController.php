<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssistanceStoreRequest;
use App\Http\Requests\AssistanceUpdateRequest;
use App\Http\Resources\AssistanceCollection;
use App\Http\Resources\AssistanceResource;
use App\Http\Resources\TypeAssistanceResource;
use App\Mail\DemandeAssistance;
use App\Models\Assistance;
use App\Models\Cotisation;
use App\Models\Credit;
use App\Models\Membre;
use App\Models\Notification;
use App\Models\TypeAssistance;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AssistanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AssistanceCollection
     */
    public function index(Request $request)
    {

        $query = Assistance::query();

        // Search
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereAny(['membre_id', 'type_assistance_id', 'montant', 'date_demande', 'date_approbation', 'date_versement', 'statut', 'justificatif'], 'LIKE', "%{$searchTerm}%")
                    ->orWhereHas('membre', function ($query) use ($searchTerm) {
                        $query->where('matricule', 'like', "%{$searchTerm}%")
                            ->orWhere('nom', 'like', "%{$searchTerm}%")
                            ->orWhere('prenom', 'like', "%{$searchTerm}%");
                    });
            });
        }

        // Filters
        if ($request->has('filter')) {
            foreach ($request->filter as $field => $value) {
                if ($value) {
                    $query->where($field, 'LIKE', "%{$value}%");
                }
            }
        }

        // Sorting
        if ($request->has('sort_field') && $request->sort_field) {
            $query->orderBy(
                $request->sort_field,
                $request->sort_order ?? 'asc'
            );
        }

        // Pagination
        $perPage = $request->per_page ?? 10;
        $assistances = $query->with("typeAssistance")->latest()->paginate($perPage);

        return sendResponse(
            $assistances,
            Response::HTTP_OK
        );
    }

    public function dashboard(Request $request)
    {
        $totalAssistances = Assistance::count();
        $totalMontantAssiste = Assistance::sum('montant');
        $typesAssistance = TypeAssistance::get();
        $detailsAssistances = Assistance::latest()->take(5)->get()->where('statut', 'approuve');

        $data = [
            'total_assistances' => $totalAssistances,
            'total_montant_assiste' => $totalMontantAssiste,
            'types_assistance' => TypeAssistanceResource::collection($typesAssistance),
            'details_assistances' => AssistanceResource::collection($detailsAssistances),
        ];

        return sendResponse($data, 'Données du tableau de bord des assistances récupérées avec succès.');
    }


    public function demandeAssistance(Request $request)
    {
        $image = $request->file('justificatif');
        //put in storage public
        $image->store('justificatif');

        $request->validate([
            'type_assistance_id' => 'required|exists:type_assistances,id',
            'montant' => 'required|numeric',
            'date_demande' => 'required|date',
            'date_approbation' => 'date',
            'date_versement' => 'date',
            'statut' => 'required|in:en_attente,approuve,rejete,verse',
            'justificatif' => 'required|string|max:255',
            'motif_rejet' => 'string',
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
                'Vous ne pouvez pas demander une assistance tant que vous avez des cotisations irrégulières ou des crédits impayés.',
                Response::HTTP_FORBIDDEN
            );
        }

        try {
            DB::beginTransaction();
            $assistance = Assistance::create([
                'montant' => $request->montant,
                'date_demande' => $request->date_demande,
                'date_approbation' => $request->date_approbation,
                'date_versement' => $request->date_versement,
                'statut' => $request->statut,
                'justificatif' => $request->justificatif,
                'motif_rejet' => $request->motif_rejet,
                'type_assistance_id' => $request->type_assistance_id,
                'membre_id' => $membre->id,
            ]);

            Notification::create([
                'type' => 'assistance',
                'title' => 'Nouvelle demande d\'assistance',
                'message' => 'Une nouvelle demande d\'assistance a ete effectuee par ' . $membre->nom . ' ' . $membre->prenom . ' pour un montant de ' . $request->montant_demande . ' BIF',
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
            Mail::to(EMAIL_COPIES)
                ->cc(auth()->user()->email)
                ->queue(new DemandeAssistance($assistance->load('membre')));
        } catch (\Exception $e) {
            return sendError($e->getMessage());
        }
        return sendResponse($assistance, 'Assistance created successfully.');
    }
    public function mesAssistances()
    {
        try {
            $membre = Membre::where('user_id', auth()->user()->id)->first()->id;
            $assistances = Assistance::where('membre_id', $membre)->latest()->paginate();
        } catch (\Exception $e) {
            return sendError($e->getMessage());
        }
        return sendResponse($assistances, 'Assistances retrieved successfully.');
    }

   public function store(AssistanceStoreRequest $request)
{
    try {
        if ($request->hasFile('justificatif') && $request->file('justificatif')->isValid()) {           
            $image = $request->file('justificatif');
            $path = $image->store('justificatif', 'public');
        } else {
            return sendError("Aucun fichier ou fichier invalide.", Response::HTTP_BAD_REQUEST);
        }

        $hasIrregularCotisations = Cotisation::where('membre_id', $request->membre_id)
            ->whereIn('statut', ['en_attente', 'en_retard'])
            ->exists();
        $hasUnpaidCredits = Credit::where('membre_id', $request->membre_id)
            ->where('montant_restant', '!=', 0)
            ->exists();

        if (!$hasIrregularCotisations && !$hasUnpaidCredits) {

            $assistance = Assistance::create($request->validated());

            return sendResponse(
                $assistance,
                Response::HTTP_CREATED,
            );
        }
    } catch (\Exception $e) {
        return sendError($e->getMessage());
    }

    return sendError(
        "Vous avez des crédits ou des cotisations irrégulières. Merci de les régulariser.",
        Response::HTTP_FORBIDDEN
    );
}



    public function dashboardAdmin(Request $request)
    {
        $totalAssistances = Assistance::count();
        $totalMontantAssiste = Assistance::sum('montant');
        $typesAssistance = TypeAssistance::get();
        $detailsAssistances = Assistance::latest()->take(5)->get()->where('statut', 'approuve');

        $data = [
            'total_assistances' => $totalAssistances,
            'total_montant_assiste' => $totalMontantAssiste,
            'types_assistance' => TypeAssistanceResource::collection($typesAssistance),
            'details_assistances' => AssistanceResource::collection($detailsAssistances),
        ];

        return sendResponse($data, 'Données du tableau de bord des assistances récupérées avec succès.');
    }

    public function show(Request $request, Assistance $assistance)
    {
        return sendResponse($assistance, 'Détails de l\'assistance récupérés avec succès.');
    }

    public function update(AssistanceUpdateRequest $request, Assistance $assistance)
    {
        $assistance->update($request->validated());

        return new AssistanceResource($assistance);
    }

    public function destroy(Request $request, Assistance $assistance)
    {
        $assistance->delete();

        return response()->noContent();
    }
}
