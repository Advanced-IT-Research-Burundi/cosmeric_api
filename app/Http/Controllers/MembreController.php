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
        $membres = Membre::all();

        return sendResponse($membres, 'Membres récupérés avec succès');
    }

    public function store(MembreStoreRequest $request)
    {
        $membre = Membre::create($request->validated());

        return sendResponse($membre, 'Membre créé avec succès');
    }

    public function show(Request $request, Membre $membre)
    {
        return sendResponse($membre, 'Membre récupéré avec succès');
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
