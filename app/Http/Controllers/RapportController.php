<?php

namespace App\Http\Controllers;

use App\Http\Requests\RapportStoreRequest;
use App\Http\Requests\RapportUpdateRequest;
use App\Http\Resources\RapportCollection;
use App\Http\Resources\RapportResource;
use App\Models\Rapport;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RapportController extends Controller
{
    public function index(Request $request): Response
    {
        $rapports = Rapport::all();

        return new RapportCollection($rapports);
    }

    public function store(RapportStoreRequest $request): Response
    {
        $rapport = Rapport::create($request->validated());

        return new RapportResource($rapport);
    }

    public function show(Request $request, Rapport $rapport): Response
    {
        return new RapportResource($rapport);
    }

    public function update(RapportUpdateRequest $request, Rapport $rapport): Response
    {
        $rapport->update($request->validated());

        return new RapportResource($rapport);
    }

    public function destroy(Request $request, Rapport $rapport): Response
    {
        $rapport->delete();

        return response()->noContent();
    }
}
