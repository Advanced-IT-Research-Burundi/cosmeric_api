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
    public function index(Request $request)
    {
        $credits = Credit::latest()->paginate();
        return sendResponse($credits, 'Credits retrieved successfully.');
    }

    public function store(CreditStoreRequest $request)    {
        $credit = Credit::create(array_merge($request->validated(), [
            'user_id' => $request->user()->id,
        ]));

        return new CreditResource($credit);
    }

    public function show(Request $request, Credit $credit)
    {
        return new CreditResource($credit);
    }

    public function update(CreditUpdateRequest $request, Credit $credit)
    {
        $credit->update($request->validated());

        return new CreditResource($credit);
    }

    public function destroy(Request $request, Credit $credit)
    {
        $credit->delete();
        return response()->noContent();
    }
}
