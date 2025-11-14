<?php

namespace App\Http\Controllers;

use App\Http\Requests\CotisationStoreRequest;
use App\Http\Requests\CotisationUpdateRequest;
use App\Http\Resources\CotisationCollection;
use App\Http\Resources\CotisationResource;
use App\Models\Cotisation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CotisationController extends Controller
{

    public function mesCotisations(){
        // get Member

        $cotisations = Cotisation::where('membre_id', auth()->user()->membre_id)->get();
        return sendResponse($cotisations, 'Cotisations retrieved successfully.');
    }
    public function index(Request $request)
    {
        $query = Cotisation::with('periode');

        // Recherche textuelle
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('reference_paiement', 'like', "%{$search}%")
                ->orWhere('membre_id', 'like', "%{$search}%");
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

        return sendResponse($cotisation, 'Cotisation créée avec succès.');
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
