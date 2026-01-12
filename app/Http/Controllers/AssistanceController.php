<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssistanceStoreRequest;
use App\Http\Requests\AssistanceUpdateRequest;
use App\Http\Resources\AssistanceCollection;
use App\Http\Resources\AssistanceResource;
use App\Http\Resources\TypeAssistanceResource;
use App\Mail\AccepteAssistance;
use App\Mail\AssistanceCreate;
use App\Mail\DemandeApprobationAssistance;
use App\Mail\DemandeAssistance;
use App\Mail\RefuserAssistance;
use App\Models\Assistance;
use App\Models\Cotisation;
use App\Models\Credit;
use App\Models\Membre;
use App\Models\Notification;
use App\Models\TypeAssistance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class AssistanceController extends Controller
{

    public function approuve($id)
    {
        // check role for connected user 
        if(!Auth::user()->hasRoles(['admin','gestionnaire','responsable'])){
            return sendError("Vous n'avez pas la permission d'approuver cette assistance.", [], Response::HTTP_FORBIDDEN);
        }

        $assistance = Assistance::findOrFail($id);

        if (auth()->user()->hasRole('gestionnaire')) {
            $assistance->update(['statut' => 'en_cours']);
            
            $responsableUsers = User::where('role', 'responsable')->get();
            $responsableEmails = $responsableUsers->pluck('email')->toArray();
            
            if ($responsableUsers->isNotEmpty()) {
                $responsableId = $responsableUsers->first()->id;
                
                Mail::to($responsableEmails)
                    ->cc($responsableEmails)
                    ->queue(new DemandeApprobationAssistance($assistance->load(['membre', 'typeAssistance'])));

                Notification::addNotification(
                    'Une demande d\'approbation d\'assistance a été faite par ' . auth()->user()->name . ' pour le membre ' . $assistance->membre->nom . ' ' . $assistance->membre->prenom . ' pour un montant de ' . $assistance->montant . ' BIF',
                    $responsableId,
                    'Demande d\'approbation d\'assistance',
                    'assistance'
                );
            }

            return sendResponse(new AssistanceResource($assistance), 'Assistance mise en cours avec succès. La validation du responsable est requise.');
            
        } else {
            $assistance->update([
                'statut' => 'approuve',
                'date_approbation' => now(),
            ]);

            // Notify the member
            Notification::addNotification(
                'Votre demande d\'assistance a été approuvée par ' . auth()->user()->name . ' pour un montant de ' . $assistance->montant . ' BIF',
                $assistance->membre->user_id,
                'Approbation d\'assistance',
                'assistance'
            );

            Mail::to($assistance->membre->email)
                ->cc(EMAIL_COPIES)
                ->queue(new AccepteAssistance($assistance->load(['membre', 'typeAssistance'])));

            return sendResponse(new AssistanceResource($assistance), 'Assistance approuvée avec succès.');
        }
    }

    public function refuser(Request $request, $id)
    {
        $assistance = Assistance::findOrFail($id);
        $assistance->update([
            'statut' => 'rejete',
            'motif_rejet' => $request->comment ?? "",
        ]);

        try {
            Mail::to($assistance->membre->email)
                ->cc(EMAIL_COPIES)
                ->queue(new RefuserAssistance($assistance->load(['membre', 'typeAssistance'])));

            Notification::create([
                'type' => 'assistance',
                'title' => 'Assistance rejetée',
                'message' => 'Votre demande d\'assistance a été rejetée par ' . auth()->user()->name . '. Motif: ' . ($request->comment ?? 'Non spécifié'),
                'time' => now(),
                'read' => false,
                'user_id' => Auth::id(),
                'assignee_id' => $assistance->membre->user_id,
            ]);
        } catch (\Throwable $th) {
            // Log error
        }

        return sendResponse(new AssistanceResource($assistance), 'Assistance refusée avec succès.');
    }


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

     $path ="";
        if ($request->hasFile('document_justificatif') && $request->file('document_justificatif')->isValid()) {
            $image = $request->file('document_justificatif');
             $image->move('uploads/justificatifs', time() . '.' . $image->getClientOriginalExtension());
             $path = 'uploads/justificatifs/' . time() . '.' . $image->getClientOriginalExtension();
        } else {
            return sendError("Aucun fichier ou fichier invalide.", [], Response::HTTP_BAD_REQUEST);
        }

        $request->validate([
            'type_assistance_id' => 'required|exists:type_assistances,id',
            'montant' => 'required|numeric',
        ]);

        try {
            $membre = Membre::where('user_id', Auth::id())->first();
            if (!$membre) {
                return sendError("Membre non trouvé.", [], Response::HTTP_NOT_FOUND);
            }

            // ✅ Check business rules before creating assistance
            $hasIrregularCotisations = Cotisation::where('membre_id', $membre->id)
                ->whereIn('statut', ['en_attente', 'en_retard'])
                ->exists();

            $hasUnpaidCredits = Credit::where('membre_id', $membre->id)
                ->where('montant_restant', '!=', 0)
                ->exists();

            if ($hasIrregularCotisations || $hasUnpaidCredits) {
                return sendError(
                    'Vous ne pouvez pas demander une assistance tant que vous avez des cotisations irrégulières ou des crédits impayés.',
                    [],
                    Response::HTTP_FORBIDDEN
                );
            }

            // Check if there is already a pending assistance
            if (Assistance::where('membre_id', $membre->id)->where('statut', 'en_attente')->exists()) {
                return sendError("Vous avez déjà une demande d'assistance en attente.", [], Response::HTTP_FORBIDDEN);
            }

            DB::beginTransaction();
            $assistance = Assistance::create([
                'montant' => $request->montant,
                'date_demande' => now(),
                'statut' => 'en_attente',
                'justificatif' => $path,
                'type_assistance_id' => $request->type_assistance_id,
                'membre_id' => $membre->id,
            ]);
            $user_id = User::where('role', 'gestionnaire')->first()->id;
            Notification::addNotification(
                'Une nouvelle demande d\'assistance a été effectuée par ' . $membre->nom . ' ' . $membre->prenom . ' pour un montant de ' . $assistance->montant . ' BIF',
                $user_id,
                'Nouvelle demande d\'assistance',
                'assistance',

            );

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
