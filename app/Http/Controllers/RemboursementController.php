<?php

namespace App\Http\Controllers;

use App\Http\Requests\RemboursementStoreRequest;
use App\Http\Requests\RemboursementUpdateRequest;
use App\Http\Resources\RemboursementCollection;
use App\Http\Resources\RemboursementResource;
use App\Models\Remboursement;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RemboursementController extends Controller
{
    public function index(Request $request): Response
    {
        $remboursements = Remboursement::all();

        return new RemboursementCollection($remboursements);
    }

    public function store(RemboursementStoreRequest $request): Response
    {
        $remboursement = Remboursement::create($request->validated());

        return new RemboursementResource($remboursement);
    }

    public function show(Request $request, Remboursement $remboursement): Response
    {
        return new RemboursementResource($remboursement);
    }

    public function update(RemboursementUpdateRequest $request, Remboursement $remboursement): Response
    {
        $remboursement->update($request->validated());

        return new RemboursementResource($remboursement);
    }

    public function destroy(Request $request, Remboursement $remboursement): Response
    {
        $remboursement->delete();

        return response()->noContent();
    }
}
