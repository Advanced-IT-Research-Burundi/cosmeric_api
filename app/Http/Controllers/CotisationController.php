<?php

namespace App\Http\Controllers;

use App\Http\Requests\CotisationStoreRequest;
use App\Http\Requests\CotisationUpdateRequest;
use App\Http\Resources\CotisationCollection;
use App\Http\Resources\CotisationResource;
use App\Models\Cotisation;
use App\Models\CotisationMensuelle;
use App\Models\Membre;
use Illuminate\Http\Request;

class CotisationController extends Controller
{

    public function remboursementsMensuelles()
    {
         $params = request()->all();
        $inputSearch = $params['params']['search'] ?? null;
        
        $page = $params['page'] ?? 1; // default page
        $perPage = $params['per_page'] ?? 15; // default per page
        $query = CotisationMensuelle::query();

        if ($inputSearch) {
            $query->where(function ($q) use ($inputSearch) {
        
                $q->where('name', 'like', '%' . $inputSearch . '%')
                    ->orWhere('matricule', 'like', '%' . $inputSearch . '%')
                    ->orWhere('retenu', 'like', '%' . $inputSearch . '%')
                    ->orWhere('date_cotisation', 'like', '%' . $inputSearch . '%');
            });
        }

        $cotisations = $query->where('type', 'REMBOURSEMENT')
        ->latest()->paginate($perPage, ['*'], 'page', $page);
        return sendResponse($cotisations, 'Cotisations mensuelles retrieved successfully.');
    }

    public function cotisationMensuelles()
    {
        /* params%5Bpage%5D=2&params%5Bper_page%5D=50&params%5Bsearch%5D=fffffffff&params%5Bsort_field%5D=&params%5Bsort_order%5D=asc */
        $params = request()->all();
        $inputSearch = $params['params']['search'] ?? null;
        
        $page = $params['page'] ?? 1; // default page
        $perPage = $params['per_page'] ?? 15; // default per page
        $query = CotisationMensuelle::query();

        if ($inputSearch) {
            $query->where(function ($q) use ($inputSearch) {
        
                $q->where('name', 'like', '%' . $inputSearch . '%')
                    ->orWhere('matricule', 'like', '%' . $inputSearch . '%')
                    ->orWhere('retenu', 'like', '%' . $inputSearch . '%')
                    ->orWhere('date_cotisation', 'like', '%' . $inputSearch . '%');
            });
        }

        $cotisations = $query->where('type', 'COTISATION')
        ->latest()->paginate($perPage, ['*'], 'page', $page);
        return sendResponse($cotisations, 'Cotisations mensuelles retrieved successfully.');
    }


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

    public function cotisationAutomatic(Request $request){

//         $request->validate([
//             'membre_id' => 'required|exists:membres,id',
//             'periode_id' => 'required|exists:periodes,id',
//             'montant' => 'required|numeric',
//             'devise' => 'required|in:BIF,USD',
//             'mode_paiement' => 'required|in:espece,virement',
//             'reference_paiement' => 'required|string|max:255',
//             'date_paiement' => 'required|date',
//         ]);

// $cotisation = Cotisation::where('membre_id', $request->membre_id)->where('periode_id', $request->periode_id)->first();
//         if($cotisation){
//             return sendError('Cotisation deja existante');
//         }

//         $cotisation = Cotisation::create([
//             'membre_id' => $request->membre_id,
//             'periode_id' => $request->periode_id,
//             'montant' => $request->montant,
//             'statut' => 'paye',
//             'devise' => $request->devise,
//             'mode_paiement' => $request->mode_paiement,
//             'reference_paiement' => $request->reference_paiement,
//             'date_paiement' => $request->date_paiement,
//         ]);

$cotisation = Cotisation::where('periode_id', $request->periode_id)->first();

if($cotisation){
    return sendError('Cotisation deja existante');
}

$membres = Membre::whereAny('statut', 'actif')->get();

for ($i=0; $i < count($membres); $i++) {
    $cotisation->create([
    'statut'=> 'en_attente',
    'membre_id' => $membres[$i]->id,
    'periode_id' => $request->periode_id,
    'montant' => $request->montant,
    'devise' => $membres[$i]->devise,
    'mode_paiement' => 'espece',
    'reference_paiement' => null,
    'date_paiement' => null,
]);

        return sendResponse($cotisation, 'Cotisation mise a jour avec succes');
    }}

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

        $cotisations = $query->latest()->paginate(10);

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
