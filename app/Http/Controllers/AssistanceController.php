<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssistanceStoreRequest;
use App\Http\Requests\AssistanceUpdateRequest;
use App\Http\Resources\AssistanceCollection;
use App\Http\Resources\AssistanceResource;
use App\Models\Assistance;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AssistanceController extends Controller
{
    public function index(Request $request): Response
    {
        $assistances = Assistance::all();

        return new AssistanceCollection($assistances);
    }

    public function store(AssistanceStoreRequest $request): Response
    {
        $assistance = Assistance::create($request->validated());

        return new AssistanceResource($assistance);
    }

    public function show(Request $request, Assistance $assistance): Response
    {
        return new AssistanceResource($assistance);
    }

    public function update(AssistanceUpdateRequest $request, Assistance $assistance): Response
    {
        $assistance->update($request->validated());

        return new AssistanceResource($assistance);
    }

    public function destroy(Request $request, Assistance $assistance): Response
    {
        $assistance->delete();

        return response()->noContent();
    }
}
