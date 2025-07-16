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
    public function index(Request $request): Response
    {
        $typeAssistances = TypeAssistance::all();

        return new TypeAssistanceCollection($typeAssistances);
    }

    public function store(TypeAssistanceStoreRequest $request): Response
    {
        $typeAssistance = TypeAssistance::create($request->validated());

        return new TypeAssistanceResource($typeAssistance);
    }

    public function show(Request $request, TypeAssistance $typeAssistance): Response
    {
        return new TypeAssistanceResource($typeAssistance);
    }

    public function update(TypeAssistanceUpdateRequest $request, TypeAssistance $typeAssistance): Response
    {
        $typeAssistance->update($request->validated());

        return new TypeAssistanceResource($typeAssistance);
    }

    public function destroy(Request $request, TypeAssistance $typeAssistance): Response
    {
        $typeAssistance->delete();

        return response()->noContent();
    }
}
