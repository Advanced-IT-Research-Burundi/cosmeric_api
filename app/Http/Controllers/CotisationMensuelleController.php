<?php

namespace App\Http\Controllers;

use App\Http\Requests\CotisationMensuelleStoreRequest;
use App\Http\Requests\CotisationMensuelleUpdateRequest;
use App\Http\Resources\CotisationMensuelleCollection;
use App\Http\Resources\CotisationMensuelleResource;
use App\Models\CotisationMensuelle;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CotisationMensuelleController extends Controller
{
    public function index(Request $request)
    {

        $query = CotisationMensuelle::query();

        // prefer explicit inputs instead of overriding $params
        $search = $request->input('name') ?? $request->input('search');
        $statut = $request->input('statut');
        $dateCotisation = $request->input('date_cotisation');

        // 🔎 Recherche : par nom ou prénom du membre, motif, ID
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('matricule', 'like', "%{$search}%")
                    ->orWhere('id', $search);
            });
        }

        // 📌 Filtre statut
        if ($statut) {
            $query->where('statut', $statut);
        }

        // 📅 Filtre date_cotisation (exact date)
        if ($dateCotisation) {
            $query->whereDate('date_cotisation', $dateCotisation);
        }

        // 📄 Pagination dynamique
        $perPage = (int) $request->input('per_page', 15);
        $cotisationMensuelles = $query->paginate($perPage);

        return sendResponse($cotisationMensuelles, 'Cotisation mensuelles retrieved successfully.');
    }

    public function store(CotisationMensuelleStoreRequest $request)
    {
        $cotisationMensuelle = CotisationMensuelle::create($request->validated());

        return new CotisationMensuelleResource($cotisationMensuelle);
    }

    public function show(Request $request, CotisationMensuelle $cotisationMensuelle)
    {
        return new CotisationMensuelleResource($cotisationMensuelle);
    }

    public function update(CotisationMensuelleUpdateRequest $request, CotisationMensuelle $cotisationMensuelle)
    {
        $cotisationMensuelle->update($request->validated());

        return new CotisationMensuelleResource($cotisationMensuelle);
    }

    public function destroy(Request $request, CotisationMensuelle $cotisationMensuelle)
    {
        $cotisationMensuelle->delete();

        return response()->noContent();
    }
}
