<?php

namespace App\Http\Controllers;

use App\Http\Requests\CotisationMensuelleStoreRequest;
use App\Http\Requests\CotisationMensuelleUpdateRequest;
use App\Http\Resources\CotisationMensuelleResource;
use App\Models\CotisationMensuelle;
use Illuminate\Http\Request;

class CotisationMensuelleController extends Controller
{
    public function index(Request $request)
    {

        
        // prefer explicit inputs instead of overriding $params
        $matricule = $request->query('params')['matricule'] ?? null;
        $name = $request->query('params')['name'] ?? null;
        $dateCotisation = $request->query('params')['date_cotisation'] ?? null;

        // 🔎 Recherche : par nom ou prénom du membre, motif, ID
        $query = CotisationMensuelle::query();
        if ($matricule) {
            $query->where('matricule', 'like', "%{$matricule}%");
        }

        if ($name) {
            $query->where('name', 'like', "%{$name}%");
        }
        if ($dateCotisation) {
            $query->where('date_cotisation', 'like', "%{$dateCotisation}%");
        }
        $cotisationMensuelles = $query->latest()->paginate();
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
