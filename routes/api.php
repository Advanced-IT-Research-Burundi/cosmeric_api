<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource('categorie-membres', App\Http\Controllers\CategorieMembreController::class);

Route::apiResource('membres', App\Http\Controllers\MembreController::class);

Route::apiResource('periodes', App\Http\Controllers\PeriodeController::class);

Route::apiResource('cotisations', App\Http\Controllers\CotisationController::class);

Route::apiResource('credits', App\Http\Controllers\CreditController::class);

Route::apiResource('remboursements', App\Http\Controllers\RemboursementController::class);

Route::apiResource('type-assistances', App\Http\Controllers\TypeAssistanceController::class);

Route::apiResource('assistances', App\Http\Controllers\AssistanceController::class);

Route::apiResource('transactions', App\Http\Controllers\TransactionController::class);

Route::apiResource('rapports', App\Http\Controllers\RapportController::class);

Route::apiResource('configurations', App\Http\Controllers\ConfigurationController::class);
