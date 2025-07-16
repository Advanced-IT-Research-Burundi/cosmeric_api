<?php

namespace App\Http\Controllers;

use App\Http\Requests\CotisationStoreRequest;
use App\Http\Requests\CotisationUpdateRequest;
use App\Http\Resources\CotisationCollection;
use App\Http\Resources\CotisationResource;
use App\Models\Cotisation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CotisationController extends Controller
{
    public function index(Request $request): Response
    {
        $cotisations = Cotisation::all();

        return new CotisationCollection($cotisations);
    }

    public function store(CotisationStoreRequest $request): Response
    {
        $cotisation = Cotisation::create($request->validated());

        return new CotisationResource($cotisation);
    }

    public function show(Request $request, Cotisation $cotisation): Response
    {
        return new CotisationResource($cotisation);
    }

    public function update(CotisationUpdateRequest $request, Cotisation $cotisation): Response
    {
        $cotisation->update($request->validated());

        return new CotisationResource($cotisation);
    }

    public function destroy(Request $request, Cotisation $cotisation): Response
    {
        $cotisation->delete();

        return response()->noContent();
    }
}
