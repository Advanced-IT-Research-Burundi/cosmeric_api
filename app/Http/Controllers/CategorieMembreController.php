<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategorieMembreStoreRequest;
use App\Http\Requests\CategorieMembreUpdateRequest;
use App\Http\Resources\CategorieMembreCollection;
use App\Http\Resources\CategorieMembreResource;
use App\Models\CategorieMembre;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategorieMembreController extends Controller
{
    public function index(Request $request)
    {
        $categorieMembres = CategorieMembre::all();

        return sendResponse($categorieMembres, 'Catégories de membres récupérées avec succès');
    }

    public function store(CategorieMembreStoreRequest $request)
    {
        $categorieMembre = CategorieMembre::create($request->validated());

        return sendResponse($categorieMembre, 'Catégorie de membre créée avec succès');
    }  

    public function show(Request $request, CategorieMembre $categorieMembre)
    {
        return sendResponse($categorieMembre, 'Catégorie de membre récupérée avec succès');
    }

    public function update(CategorieMembreUpdateRequest $request, CategorieMembre $categorieMembre)
    {
        $categorieMembre->update($request->validated());

        return sendResponse($categorieMembre, 'Catégorie de membre mise à jour avec succès');
    }

    public function destroy(Request $request, CategorieMembre $categorieMembre)
    {
        $categorieMembre->delete();

        return sendResponse(null, 'Catégorie de membre supprimée avec succès');
    }
}
