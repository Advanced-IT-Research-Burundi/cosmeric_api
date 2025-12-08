<?php

namespace App\Http\Controllers;

use App\Http\Requests\CotisationStoreRequest;
use App\Http\Requests\CotisationUpdateRequest;
use App\Http\Resources\CotisationCollection;
use App\Http\Resources\CotisationResource;
use App\Models\Cotisation;
use App\Models\CotisationMensuelle;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CotisationController extends Controller
{


    public function dashboard()
    {
        $totalCotisationsBif = Cotisation::where('devise', 'BIF')->count();
        $totalCotisationsUSD = Cotisation::where('devise', 'USD')->count();
        // $totalMembres = Cotisation::distinct('membre_id')->count('membre_id');
        $totalMembres = Cotisation::where('statut', 'paye')->count();
        $totalMontantCotisations = Cotisation::sum('montant');
        $cotisationsParPeriode = Cotisation::selectRaw('periode_id, COUNT(*) as total, SUM(montant) as total_montant')
            ->groupBy('periode_id')
            ->with('periode')
            ->get();

        $data = [
            'total_cotisations_bif' => $totalCotisationsBif,
            'total_cotisations_usd' => $totalCotisationsUSD,
            'total_membres' => $totalMembres,
            'total_montant_cotisations' => $totalMontantCotisations,
            'cotisations_par_periode' => $cotisationsParPeriode,
        ];

        return sendResponse($data, 'Données du tableau de bord des cotisations récupérées avec succès.');
    }

    public function mesCotisations()
    {
        // get Member
        $member = auth()->user()->membre;
        $cotisations = CotisationMensuelle::where('matricule', $member->user_id)
            ->latest()->paginate();
        return sendResponse($cotisations, 'Cotisations retrieved successfully.');
    }
    public function index(Request $request)
    {
        $query = Cotisation::with(['membre', 'periode']);

        // Recherche textuelle
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('reference_paiement', 'like', "%{$search}%")
                    ->orWhere('membre_id', 'like', "%{$search}%")
                    ->orWhereHas('membre', function ($query) use ($search) {
                        $query->where('matricule', 'like', "%{$search}%")
                            ->orWhere('nom', 'like', "%{$search}%")
                            ->orWhere('prenom', 'like', "%{$search}%")
                        ;
                    })
                ;
            });
        }

        // Filtres
        if ($request->has('statut')) {
            $query->where('statut', $request->get('statut'));
        }

        if ($request->has('devise')) {
            $query->where('devise', $request->get('devise'));
        }

        if ($request->has('mode_paiement')) {
            $query->where('mode_paiement', $request->get('mode_paiement'));
        }

        if ($request->has('membre_id')) {
            $query->where('membre_id', $request->get('membre_id'));
        }

        if ($request->has('periode_id')) {
            $query->where('periode_id', $request->get('periode_id'));
        }

        if ($request->has('montant_min')) {
            $query->where('montant', '>=', $request->get('montant_min'));
        }

        if ($request->has('montant_max')) {
            $query->where('montant', '<=', $request->get('montant_max'));
        }

        if ($request->has('date_debut')) {
            $query->whereDate('date_paiement', '>=', $request->get('date_debut'));
        }

        if ($request->has('date_fin')) {
            $query->whereDate('date_paiement', '<=', $request->get('date_fin'));
        }

        $cotisations = $query->paginate(10);

        return sendResponse($cotisations, 'Cotisations retrieved successfully.');
    }

    public function store(CotisationStoreRequest $request)
    {
        $cotisation = Cotisation::create($request->validated());

        return sendResponse(new CotisationResource($cotisation), 'Cotisation créée avec succès.');
    }

    public function show(Request $request, Cotisation $cotisation)
    {
        return sendResponse($cotisation, 'Détails de la cotisation récupérés avec succès.');
    }

    public function update(CotisationUpdateRequest $request, Cotisation $cotisation)
    {
        $cotisation->update($request->validated());

        return sendResponse($cotisation, 'Cotisation mise à jour avec succès.');
    }

    public function destroy(Request $request, Cotisation $cotisation)
    {
        $cotisation->delete();

        return sendResponse(null, 'Cotisation supprimée avec succès.');
    }
}
