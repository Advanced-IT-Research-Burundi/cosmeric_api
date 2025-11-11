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
    public function index(Request $request): Response
    {
        $cotisationMensuelles = CotisationMensuelle::all();

        return new CotisationMensuelleCollection($cotisationMensuelles);
    }

    public function store(CotisationMensuelleStoreRequest $request): Response
    {
        $cotisationMensuelle = CotisationMensuelle::create($request->validated());

        return new CotisationMensuelleResource($cotisationMensuelle);
    }

    public function show(Request $request, CotisationMensuelle $cotisationMensuelle): Response
    {
        return new CotisationMensuelleResource($cotisationMensuelle);
    }

    public function update(CotisationMensuelleUpdateRequest $request, CotisationMensuelle $cotisationMensuelle): Response
    {
        $cotisationMensuelle->update($request->validated());

        return new CotisationMensuelleResource($cotisationMensuelle);
    }

    public function destroy(Request $request, CotisationMensuelle $cotisationMensuelle): Response
    {
        $cotisationMensuelle->delete();

        return response()->noContent();
    }
}
