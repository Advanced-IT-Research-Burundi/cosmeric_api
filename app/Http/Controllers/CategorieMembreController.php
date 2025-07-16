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
    public function index(Request $request): Response
    {
        $categorieMembres = CategorieMembre::all();

        return new CategorieMembreCollection($categorieMembres);
    }

    public function store(CategorieMembreStoreRequest $request): Response
    {
        $categorieMembre = CategorieMembre::create($request->validated());

        return new CategorieMembreResource($categorieMembre);
    }

    public function show(Request $request, CategorieMembre $categorieMembre): Response
    {
        return new CategorieMembreResource($categorieMembre);
    }

    public function update(CategorieMembreUpdateRequest $request, CategorieMembre $categorieMembre): Response
    {
        $categorieMembre->update($request->validated());

        return new CategorieMembreResource($categorieMembre);
    }

    public function destroy(Request $request, CategorieMembre $categorieMembre): Response
    {
        $categorieMembre->delete();

        return response()->noContent();
    }
}
