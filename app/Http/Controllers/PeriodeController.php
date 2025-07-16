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
    public function index(Request $request): Response
    {
        $periodes = Periode::all();

        return new PeriodeCollection($periodes);
    }

    public function store(PeriodeStoreRequest $request): Response
    {
        $periode = Periode::create($request->validated());

        return new PeriodeResource($periode);
    }

    public function show(Request $request, Periode $periode): Response
    {
        return new PeriodeResource($periode);
    }

    public function update(PeriodeUpdateRequest $request, Periode $periode): Response
    {
        $periode->update($request->validated());

        return new PeriodeResource($periode);
    }

    public function destroy(Request $request, Periode $periode): Response
    {
        $periode->delete();

        return response()->noContent();
    }
}
