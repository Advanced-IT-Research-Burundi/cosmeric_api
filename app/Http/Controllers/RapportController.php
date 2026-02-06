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

    public function destroy(Request $request, Rapport $rapport)
    {
        $rapport->delete();

        return response()->noContent();
    }
}
