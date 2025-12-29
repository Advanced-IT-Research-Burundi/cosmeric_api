<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssistanceStoreRequest;
use App\Http\Requests\AssistanceUpdateRequest;
use App\Http\Resources\AssistanceCollection;
use App\Http\Resources\AssistanceResource;
use App\Http\Resources\TypeAssistanceResource;
use App\Mail\AssistanceCreate;
use App\Mail\DemandeAssistance;
use App\Models\Assistance;
use App\Models\Cotisation;
use App\Models\Credit;
use App\Models\Membre;
use App\Models\Notification;
use App\Models\TypeAssistance;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class AssistanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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
        if ($request->hasFile('justificatif') && $request->file('justificatif')->isValid()) {
            $image = $request->file('justificatif');
            $path = $image->store('justificatif', 'public');
        } else {
            return sendError("Aucun fichier ou fichier invalide.", [], Response::HTTP_BAD_REQUEST);
        }

        $request->validate([
            'type_assistance_id' => 'required|exists:type_assistances,id',
            'montant' => 'required|numeric',
            'date_demande' => 'required|date',
            'statut' => 'required|in:en_attente,approuve,rejete,verse',
        ]);

        try {
            $membre = Membre::where('user_id', Auth::id())->first();
            if (!$membre) {
                return sendError("Membre non trouvé.", [], Response::HTTP_NOT_FOUND);
            }

            // Check if there is already a pending assistance
            if (Assistance::where('membre_id', $membre->id)->where('statut', 'en_attente')->exists()) {
                return sendError("Vous avez déjà une demande d'assistance en attente.", [], Response::HTTP_FORBIDDEN);
            }

            DB::beginTransaction();
            $assistance = Assistance::create([
                'montant' => $request->montant,
                'date_demande' => $request->date_demande,
                'statut' => $request->statut,
                'justificatif' => $path,
                'type_assistance_id' => $request->type_assistance_id,
                'membre_id' => $membre->id,
            ]);

            Notification::create([
                'type' => 'assistance',
                'title' => 'Nouvelle demande d\'assistance',
                'message' => 'Une nouvelle demande d\'assistance a ete effectuee par ' . $membre->nom . ' ' . $membre->prenom . ' pour un montant de ' . $assistance->montant . ' BIF',
                'time' => now(),
                'read' => false,
                'user_id' => Auth::id(),
            ]);

            DB::commit();

            // Notify admins
            Mail::to(EMAIL_COPIES)->send(new AssistanceCreate($assistance));

            return sendResponse($assistance, 'Demande enregistrée avec succès', Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();
            return sendError($th->getMessage());
        }
    }

    public function mesAssistances()
    {
        try {
            $membre = Membre::where('user_id', Auth::id())->first();
            if (!$membre) {
                return sendError("Membre non trouvé.", [], Response::HTTP_NOT_FOUND);
            }
            $assistances = Assistance::where('membre_id', $membre->id)->latest()->paginate();
            return sendResponse($assistances, 'Assistances retrieved successfully.');
        } catch (\Exception $e) {
            return sendError($e->getMessage());
        }
    }

    public function store(AssistanceStoreRequest $request)
    {
        try {
            if ($request->hasFile('justificatif') && $request->file('justificatif')->isValid()) {
                $path = $request->file('justificatif')->store('justificatif', 'public');
            } else {
                return sendError("Aucun fichier ou fichier invalide.", [], Response::HTTP_BAD_REQUEST);
            }

            $hasIrregularCotisations = Cotisation::where('membre_id', $request->membre_id)
                ->whereIn('statut', ['en_attente', 'en_retard'])
                ->exists();
            $hasUnpaidCredits = Credit::where('membre_id', $request->membre_id)
                ->where('montant_restant', '!=', 0)
                ->exists();

            if (!$hasIrregularCotisations && !$hasUnpaidCredits) {
                $data = $request->validated();
                $data['justificatif'] = $path;

                $assistance = Assistance::create($data);

                Notification::create([
                    'type' => 'assistance',
                    'title' => 'Nouvelle demande d\'assistance',
                    'message' => 'Une nouvelle demande d\'assistance a ete effectuee par ' . $assistance->membre->nom . ' ' . $assistance->membre->prenom . ' pour un montant de ' . $assistance->montant . ' BIF',
                    'time' => now(),
                    'read' => false,
                    'user_id' => Auth::id(),
                ]);

                Mail::to(EMAIL_COPIES)->send(new AssistanceCreate($assistance));

                return sendResponse($assistance, 'Assistance créée avec succès', Response::HTTP_CREATED);
            }

            return sendError("Vous avez des crédits ou des cotisations irrégulières.", [], Response::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            return sendError($e->getMessage());
        }
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
        $data = $request->validated();

        if ($request->hasFile('justificatif') && $request->file('justificatif')->isValid()) {
            // Delete old file if exists
            if ($assistance->justificatif && Storage::disk('public')->exists($assistance->justificatif)) {
                Storage::disk('public')->delete($assistance->justificatif);
            }
            $data['justificatif'] = $request->file('justificatif')->store('justificatif', 'public');
        }

        $oldStatus = $assistance->statut;
        $assistance->update($data);

        // If status changed to approved, rejete, or verse, we notify here
        if ($oldStatus !== $assistance->statut) {
             Notification::create([
                'type' => 'assistance',
                'title' => 'Mise à jour de l\'assistance',
                'message' => 'Votre demande d\'assistance est passée à l\'état: ' . $assistance->statut,
                'time' => now(),
                'read' => false,
                'user_id' => $assistance?->membre?->user_id,
            ]);
        }

        return sendResponse(new AssistanceResource($assistance), 'Assistance mise à jour avec succès.');
    }

    public function destroy(Request $request, Assistance $assistance)
    {
        $assistance->delete();

        return response()->noContent();
    }
}
