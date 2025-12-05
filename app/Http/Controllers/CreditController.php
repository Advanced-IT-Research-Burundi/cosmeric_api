<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreditStoreRequest;
use App\Http\Requests\CreditUpdateRequest;
use App\Http\Resources\CreditCollection;
use App\Http\Resources\CreditResource;
use App\Models\Credit;
use Illuminate\Http\Request;

class CreditController extends Controller
{
    public function demandeCredit(Request $request){
        $request->validate([
            'montant_demande' => 'required|numeric',
            'taux_interet' => 'required|numeric',
            'duree_mois' => 'required|numeric',
            'montant_total_rembourser' => 'required|numeric',
            'montant_mensualite' => 'required|numeric',
        ]);

        $credit = Credit::create([
            'montant_demande' => $request->montant_demande,
            'taux_interet' => $request->taux_interet,
            'duree_mois' => $request->duree_mois,
            'montant_total_rembourser' => $request->montant_total_rembourser,
            'montant_mensualite' => $request->montant_mensualite,
            'membre_id' => $request->user()->id,
            'user_id' => $request->user()->id,
            'statut' => 'en_attente',
            'date_demande' => now(),
            'motif' => $request->motif,
            'montant_accorde' => 0,
        ]);

        return sendResponse($credit, 'Credit created successfully.');
        
    }
    public function mesCredits(){
        $credits = Credit::where('membre_id', auth()->user()->id)->latest()->paginate();
        return sendResponse($credits, 'Credits retrieved successfully.');
    }
    public function index(Request $request)
    {
        $query = Credit::query();

        // Search
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereAny(['montant_demande', 'montant_accorde', 'taux_interet', 'duree_mois', 'montant_total_rembourser', 'montant_mensualite'], 'LIKE', "%{$searchTerm}%");
            })
            ->orWhereHas('membre', function ($q) use ($searchTerm) {
                $q->whereAny(['nom', 'prenom', 'matricule'], 'LIKE', "%{$searchTerm}%");
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
        $credits = $query->paginate($perPage);
        return sendResponse($credits, 'Credits retrieved successfully.');
    }

    public function store(CreditStoreRequest $request)    {
        $credit = Credit::create(array_merge($request->validated(), [
            'user_id' => $request->user()->id ?? 1,
        ]));

        return new CreditResource($credit);
    }

    public function show(Request $request, Credit $credit)
    {
        return new CreditResource($credit);
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
