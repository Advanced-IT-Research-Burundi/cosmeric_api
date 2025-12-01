<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionStoreRequest;
use App\Http\Requests\TransactionUpdateRequest;
use App\Http\Resources\TransactionCollection;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = Transaction::all();

        return sendResponse(new TransactionCollection($transactions), "Listes des transactions", 200);
    }

    public function store(TransactionStoreRequest $request)
    {
        $transaction = Transaction::create($request->validated());

        return sendResponse(new TransactionResource($transaction), "Transaction bien recus", 200);;
    }

    public function show(Request $request, Transaction $transaction)
    {
        return new TransactionResource($transaction);
    }

    public function update(TransactionUpdateRequest $request, Transaction $transaction)
    {
        $transaction->update($request->validated());

        return new TransactionResource($transaction);
    }

    public function destroy(Request $request, Transaction $transaction)
    {
        $transaction->delete();

        return response()->noContent();
    }
}
