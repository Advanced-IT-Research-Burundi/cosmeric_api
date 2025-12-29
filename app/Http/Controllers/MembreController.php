<?php

namespace App\Http\Controllers;

use App\Http\Requests\MembreStoreRequest;
use App\Http\Requests\MembreUpdateRequest;

use App\Http\Resources\MembreCollection;
use App\Http\Resources\MembreResource;
use App\Models\Membre;
use Illuminate\Http\Request;

class MembreController extends Controller
{
    public function index(Request $request)
    {
        $query = Membre::with('categorie');

        // Search
        if ($request->filled('search')) {
            $searchTerm = $request->search;

            $query->where(function ($q) use ($searchTerm) {
                $q->where('matricule', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('nom', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('prenom', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('email', 'LIKE', "%{$searchTerm}%");
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
            if ($request->sort_field === 'full_name') {
                $query->orderByRaw("CONCAT(nom, ' ', prenom) " . ($request->sort_order ?? 'asc'));
            } else if ($request->sort_field === 'categorie.description') {
                $query->join('categorie_membres', 'membres.categorie_id', '=', 'categorie_membres.id')
                    ->orderBy('categorie_membres.description', $request->sort_order ?? 'asc');
            } else {
                $query->orderBy($request->sort_field, $request->sort_order ?? 'asc');
            }
        }

        // Pagination
        $perPage = $request->per_page ?? 10;
        $membres = $query->paginate($perPage);

        // Format response
        return sendResponse($membres, 'Membres récupérés avec succès');
    }

    public function search(Request $request)
{
    $search = $request->get('q');
    dd($search);

    // Search for members based on provided query
    $membres = Membre::with('categorie')
        ->when($search, function ($query) use ($search) {
            $query->where('matricule', 'like', "%{$search}%")
                  ->orWhere('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%");
        })
        ->limit(10) // Limit for autocomplete
        ->get();

    // Check if results exist
    if ($membres->isEmpty()) {
        return response()->json(['message' => 'No members found'], 404);
    }

    return response()->json($membres);
}

    public function store(MembreStoreRequest $request)
    {

        $membre = Membre::create($request->validated());

        return sendResponse($membre, 'Membre créé avec succès');
    }

    public function show(Request $request, Membre $membre)
    {


        $membre->load('user');
        $membre->load('categorie');
        $membre->load('cotisations');
        $membre->load('credits');
        $membre->load('assistances');

        $data = $membre;

        return sendResponse(new MembreResource($data), 'Membre récupéré avec succès');
    }

    public function update(MembreUpdateRequest $request, Membre $membre)
    {
        $membre->update($request->validated());

        return sendResponse($membre, 'Membre mis à jour avec succès');
    }

    public function destroy(Request $request, Membre $membre)
    {
        $membre->delete();

        return sendResponse(null, 'Membre supprimé avec succès');
    }
}
