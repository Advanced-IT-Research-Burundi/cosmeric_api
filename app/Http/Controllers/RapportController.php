<?php

namespace App\Http\Controllers;

use App\Http\Requests\RapportStoreRequest;
use App\Http\Requests\RapportUpdateRequest;
use App\Http\Resources\RapportCollection;
use App\Http\Resources\RapportResource;
use App\Models\Rapport;
use App\Models\Cotisation;
use App\Models\Membre;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RapportController extends Controller
{
    public function index(Request $request)
    {
        $totalCotisationsBif = Cotisation::where('devise', 'BIF')->count();
        $totalCotisationsUSD = Cotisation::where('devise', 'USD')->count();
        // $totalMembres = Cotisation::distinct('membre_id')->count('membre_id');
        $totalMembres = Membre::where('statut', 'actif')->count();
        $taux = 3;
        $cotisationsParMembre = Cotisation::selectRaw('membre_id, COUNT(*) as total, SUM(montant) as total_montant')
            ->groupBy('membre_id')
            ->with('membre')
            ->get();

        $data = [
            'total_cotisations_bif' => $totalCotisationsBif,
            'total_cotisations_usd' => $totalCotisationsUSD,
            'total_membres' => $totalMembres,
            'taux' => $taux,
            'cotisations_par_membre' => $cotisationsParMembre,
        ];

        return sendResponse($data, 'Données du tableau de bord des cotisations récupérées avec succès.');
    }



    public function store(RapportStoreRequest $request)
    {
        $rapport = Rapport::create($request->validated());

        return new RapportResource($rapport);
    }

    public function show(Request $request, Rapport $rapport)
    {
        return new RapportResource($rapport);
    }

    public function update(RapportUpdateRequest $request, Rapport $rapport)
    {
        $rapport->update($request->validated());

        return new RapportResource($rapport);
    }

    public function getCreditReport(Request $request)
    {
        $query = \App\Models\Credit::query();
        $this->applyFilters($query, $request);

        $summary = [
            'total_demande' => $query->sum('montant_demande'),
            'total_accorde' => $query->sum('montant_accorde'),
            'count' => $query->count(),
            'total_restant' => $query->sum('montant_total_rembourser') - \App\Models\Remboursement::whereIn('credit_id', $query->pluck('id'))->sum('montant_paye')
        ];

        $transactions = $query->latest()->limit(10)->get();

        return sendResponse(['summary' => $summary, 'transactions' => $transactions], 'Rapport de crédit récupéré avec succès.');
    }

    public function getAssistanceReport(Request $request)
    {
        $query = \App\Models\Assistance::query();
        $this->applyFilters($query, $request);

        $summary = [
            'total_montant' => $query->sum('montant'),
            'count' => $query->count(),
            'approuve' => (clone $query)->where('statut', 'approuvé')->count(),
            'rejete' => (clone $query)->where('statut', 'rejeté')->count(),
        ];

        $transactions = $query->latest()->limit(10)->get();

        return sendResponse(['summary' => $summary, 'transactions' => $transactions], 'Rapport d\'assistance récupéré avec succès.');
    }

    public function getRemboursementReport(Request $request)
    {
        $query = \App\Models\Remboursement::query();
        
        if ($request->filled('categorie_id')) {
            $query->whereHas('credit.membre', function ($q) use ($request) {
                $q->where('categorie_id', $request->categorie_id);
            });
        }
        
        if ($request->filled('date_debut')) {
            $query->where('date_paiement', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->where('date_paiement', '<=', $request->date_fin);
        }

        $summary = [
            'total_paye' => $query->sum('montant_paye'),
            'total_penalite' => $query->sum('penalite'),
            'count' => $query->count(),
            'en_retard' => (clone $query)->where('statut', 'retard')->count(),
        ];

        $transactions = $query->with('credit.membre')->latest()->limit(10)->get();

        return sendResponse(['summary' => $summary, 'transactions' => $transactions], 'Rapport de remboursement récupéré avec succès.');
    }

    public function getCotisationReport(Request $request)
    {
        $query = \App\Models\Cotisation::query();
        $this->applyFilters($query, $request);

        $summary = [
            'total_bif' => (clone $query)->where('devise', 'BIF')->sum('montant'),
            'total_usd' => (clone $query)->where('devise', 'USD')->sum('montant'),
            'count' => $query->count(),
            'en_retard' => (clone $query)->where('statut', 'en_retard')->count(),
        ];

        $transactions = $query->latest()->limit(10)->get();

        return sendResponse(['summary' => $summary, 'transactions' => $transactions], 'Rapport de cotisation récupéré avec succès.');
    }

    private function applyFilters($query, Request $request)
    {
        if ($request->filled('categorie_id')) {
            $query->whereHas('membre', function ($q) use ($request) {
                $q->where('categorie_id', $request->categorie_id);
            });
        }

        if ($request->filled('date_debut')) {
            $model = $query->getModel();
            $dateField = 'created_at';
            if ($model instanceof \App\Models\Cotisation) $dateField = 'date_paiement';
            elseif ($model instanceof \App\Models\Credit) $dateField = 'date_demande';
            elseif ($model instanceof \App\Models\Assistance) $dateField = 'created_at';
            
            $query->where($dateField, '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $model = $query->getModel();
            $dateField = 'created_at';
            if ($model instanceof \App\Models\Cotisation) $dateField = 'date_paiement';
            elseif ($model instanceof \App\Models\Credit) $dateField = 'date_demande';
            elseif ($model instanceof \App\Models\Assistance) $dateField = 'created_at';
            
            $query->where($dateField, '<=', $request->date_fin);
        }
    }
}
