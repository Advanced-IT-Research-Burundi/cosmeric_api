<?php

use App\Http\Controllers\AssistanceController;
use App\Http\Controllers\CategorieMembreController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\CotisationController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImportationController;
use App\Http\Controllers\MembreController;
use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\RemboursementController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TypeAssistanceController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');




// Routes publiques
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

// Routes protégées
Route::middleware('auth:sanctum')->group(function () {
    // Authentification
    Route::post('/logout', [UserController::class, 'logout']);
    Route::post('/logout-all', [UserController::class, 'logoutAll']);
    Route::get('/me', [UserController::class, 'me']);
    Route::post('/refresh-token', [UserController::class, 'refreshToken']);
    Route::post('/check-token', [UserController::class, 'checkToken']);

    // Profil
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::post('/change-password', [UserController::class, 'changePassword']);

    // Gestion des tokens
    Route::get('/tokens', [UserController::class, 'tokens']);
    Route::delete('/tokens', [UserController::class, 'revokeToken']);

    Route::apiResource('categorie-membres', CategorieMembreController::class);
    Route::apiResource('membres', MembreController::class);
    Route::apiResource('periodes', PeriodeController::class);
    Route::apiResource('cotisations', CotisationController::class);
    Route::apiResource('credits', CreditController::class);
    Route::apiResource('remboursements', RemboursementController::class);
    Route::apiResource('type-assistances', TypeAssistanceController::class);
    Route::apiResource('assistances', AssistanceController::class);
    Route::apiResource('transactions', TransactionController::class);
    Route::apiResource('rapports', RapportController::class);
    Route::apiResource('configurations', ConfigurationController::class);
    Route::apiResource('users', UserController::class);
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::post("importation", [ImportationController::class , 'cotisation']);
    Route::apiResource('cotisation-mensuelles', App\Http\Controllers\CotisationMensuelleController::class);


});




