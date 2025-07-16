<?php

namespace App\Http\Controllers;

use App\Http\Requests\MembreStoreRequest;
use App\Http\Requests\MembreUpdateRequest;
use App\Http\Resources\MembreCollection;
use App\Http\Resources\MembreResource;
use App\Models\Membre;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MembreController extends Controller
{
    public function index(Request $request): Response
    {
        $membres = Membre::all();

        return new MembreCollection($membres);
    }

    public function store(MembreStoreRequest $request): Response
    {
        $membre = Membre::create($request->validated());

        return new MembreResource($membre);
    }

    public function show(Request $request, Membre $membre): Response
    {
        return new MembreResource($membre);
    }

    public function update(MembreUpdateRequest $request, Membre $membre): Response
    {
        $membre->update($request->validated());

        return new MembreResource($membre);
    }

    public function destroy(Request $request, Membre $membre): Response
    {
        $membre->delete();

        return response()->noContent();
    }
}
