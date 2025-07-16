<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreditStoreRequest;
use App\Http\Requests\CreditUpdateRequest;
use App\Http\Resources\CreditCollection;
use App\Http\Resources\CreditResource;
use App\Models\Credit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CreditController extends Controller
{
    public function index(Request $request): Response
    {
        $credits = Credit::all();

        return new CreditCollection($credits);
    }

    public function store(CreditStoreRequest $request): Response
    {
        $credit = Credit::create($request->validated());

        return new CreditResource($credit);
    }

    public function show(Request $request, Credit $credit): Response
    {
        return new CreditResource($credit);
    }

    public function update(CreditUpdateRequest $request, Credit $credit): Response
    {
        $credit->update($request->validated());

        return new CreditResource($credit);
    }

    public function destroy(Request $request, Credit $credit): Response
    {
        $credit->delete();

        return response()->noContent();
    }
}
