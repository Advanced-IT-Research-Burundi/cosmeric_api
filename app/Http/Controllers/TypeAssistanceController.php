<?php

namespace App\Http\Controllers;

use App\Http\Requests\TypeAssistanceStoreRequest;
use App\Http\Requests\TypeAssistanceUpdateRequest;
use App\Http\Resources\TypeAssistanceCollection;
use App\Http\Resources\TypeAssistanceResource;
use App\Models\TypeAssistance;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TypeAssistanceController extends Controller
{
    public function index(Request $request)
    {
        $typeAssistances = TypeAssistance::all();

        return sendResponse($typeAssistances, 'Liste des types d\'assistance récupérée avec succès.');
    }

    public function store(TypeAssistanceStoreRequest $request)
    {
        $typeAssistance = TypeAssistance::create($request->validated());

        return sendResponse($typeAssistance, 'Type d\'assistance créé avec succès.');
    }

    public function show(Request $request, TypeAssistance $typeAssistance)
    {
        return sendResponse($typeAssistance, 'Détails du type d\'assistance récupérés avec succès.');
    }

    public function update(TypeAssistanceUpdateRequest $request, TypeAssistance $typeAssistance)
    {
        $typeAssistance->update($request->validated());

        return sendResponse($typeAssistance, 'Type d\'assistance mis à jour avec succès.');
    }

    public function destroy(Request $request, TypeAssistance $typeAssistance)
    {
        $typeAssistance->delete();

        return sendResponse(null, 'Type d\'assistance supprimé avec succès.');
    }
}
