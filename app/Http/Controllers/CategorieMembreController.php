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

        return sendResponse($categorieMembres, 'Categorie Membres retrieved successfully.'); 
    }

    public function store(CategorieMembreStoreRequest $request)
    {
        $categorieMembre = CategorieMembre::create($request->validated());
        return sendResponse($categorieMembre, 'Categorie Membre created successfully.');
    }

    public function show(Request $request, CategorieMembre $categorieMembre)
    {
        return sendResponse($categorieMembre, 'Categorie Membre retrieved successfully.');
    }

    public function update(CategorieMembreUpdateRequest $request, CategorieMembre $categorieMembre)
    {
        $categorieMembre->update($request->validated());

        return sendResponse($categorieMembre, 'Categorie Membre updated successfully.');
    }

    public function destroy(Request $request, CategorieMembre $categorieMembre)
    {
        $categorieMembre->delete();

        return response()->noContent();
    }
}
