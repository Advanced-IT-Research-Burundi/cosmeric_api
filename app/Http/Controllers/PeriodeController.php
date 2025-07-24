<?php

namespace App\Http\Controllers;

use App\Http\Requests\PeriodeStoreRequest;
use App\Http\Requests\PeriodeUpdateRequest;
use App\Http\Resources\PeriodeCollection;
use App\Http\Resources\PeriodeResource;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PeriodeController extends Controller
{
    public function index(Request $request)
    {
        $periodes = Periode::all();

        return sendResponse($periodes, 'Liste des periodes récupérée avec succès.');
    }

    public function store(PeriodeStoreRequest $request)
    {
        $periode = Periode::create($request->validated());

        return sendResponse($periode, 'Periode créée avec succès.');
    }

    public function show(Request $request, Periode $periode)
    {
        return sendResponse($periode, 'Détails de la periode récupérés avec succès.');
    }

    public function update(PeriodeUpdateRequest $request, Periode $periode)
    {
        $periode->update($request->validated());

        return sendResponse($periode, 'Periode mise à jour avec succès.');
    }

    public function destroy(Request $request, Periode $periode)
    {
        $periode->delete();

        return sendResponse(null, 'Periode supprimée avec succès.');
    }
}
