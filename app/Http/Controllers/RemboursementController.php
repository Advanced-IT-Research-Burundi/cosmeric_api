<?php

namespace App\Http\Controllers;

use App\Http\Requests\RemboursementStoreRequest;
use App\Http\Requests\RemboursementUpdateRequest;
use App\Http\Resources\RemboursementCollection;
use App\Http\Resources\RemboursementResource;
use App\Models\Credit;
use App\Models\Remboursement;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RemboursementController extends Controller
{
    public function index(Request $request)
    {
        $query = Remboursement::query();

        // Search
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('montant_prevu', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('montant_paye', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('numero_echeance', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('statut', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('penalite', 'LIKE', "%{$searchTerm}%");
            })
                ->orWhereHas('credit.membre', function ($q) use ($searchTerm) {
                    $q->where('nom', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('prenom', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('matricule', 'LIKE', "%{$searchTerm}%");
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
        $remboursements = $query->paginate($perPage);

        return sendResponse($remboursements, 'Remboursements récupérés avec succès');
    }

    public function store(RemboursementStoreRequest $request)
    {
        $remboursement = Remboursement::create($request->validated());

        return sendResponse($remboursement, 'Remboursement créé avec succès', 201);
    }

    public function show(Request $request, Remboursement $remboursement)
    {
        return sendResponse($remboursement, 'Remboursement récupéré avec succès');
    }

    public function update(RemboursementUpdateRequest $request, Remboursement $remboursement)
    {
        $remboursement->update($request->validated());

        return sendResponse($remboursement, 'Remboursement mis à jour avec succès');
    }

    public function destroy(Request $request, Remboursement $remboursement)
    {
        $remboursement->delete();

        return response()->noContent();
    }

    /**
     * Permet à un administrateur de marquer une échéance comme payée.
     */
    public function approve(Request $request, Remboursement $remboursement)
    {
        $request->validate([
            'montant_paye' => 'nullable|numeric|min:0',
            'date_paiement' => 'nullable|date',
        ]);

        $montantPaye = $request->input('montant_paye', $remboursement->montant_prevu);
        $datePaiement = $request->input('date_paiement', now());

        // Mettre à jour le statut (en retard si paiement après la date d'échéance)
        $statut = 'paye';
        $penalite = $remboursement->penalite ?? 0;

        if ($datePaiement->greaterThan($remboursement->date_echeance)) {
            $statut = 'paye';
            // Ici vous pouvez appliquer une logique de calcul de pénalité si nécessaire
        }

        $remboursement->update([
            'montant_paye' => $montantPaye,
            'date_paiement' => $datePaiement,
            'statut' => $statut,
            'penalite' => $penalite,
        ]);

        // Mettre à jour le crédit lié
        $credit = $remboursement->credit()->with('remboursements')->first();

        $totalPaye = $credit->remboursements->sum('montant_paye');
        $credit->montant_restant = max(0, $credit->montant_total_rembourser - $totalPaye);
        $credit->statut = $credit->montant_restant <= 0 ? 'termine' : 'en_cours';
        $credit->save();

        return sendResponse($remboursement->fresh(), 'Échéance approuvée (payée) avec succès');
    }
}
