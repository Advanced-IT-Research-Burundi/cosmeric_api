<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class UserController extends Controller
{
    // ===== INSCRIPTION =====
    public function register(Request $request): JsonResponse
    {
        try {
            // Validation des données
            $validator = Validator::make($request->all(), [
                'nom' => ['required', 'string', 'max:100'],
                'prenom' => ['required', 'string', 'max:100'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'telephone' => ['nullable', 'string', 'max:20'],
                'password' => ['required', 'confirmed', Password::defaults()],
                'role' => ['required', 'in:admin,gestionnaire,membre'],
            ], [
                'nom.required' => 'Le nom est obligatoire.',
                'prenom.required' => 'Le prénom est obligatoire.',
                'email.required' => 'L\'adresse email est obligatoire.',
                'email.unique' => 'Cette adresse email est déjà utilisée.',
                'email.email' => 'L\'adresse email doit être valide.',
                'password.required' => 'Le mot de passe est obligatoire.',
                'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
                'role.required' => 'Le rôle est obligatoire.',
                'role.in' => 'Le rôle doit être admin, gestionnaire ou membre.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Création de l'utilisateur
            $user = User::create([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            // Création du token
            $tokenName = $request->device_name ?? 'registration_token';
            $abilities = $this->getTokenAbilities($user->role);
            $expiresAt = now()->addHours(24);

            $token = $user->createToken($tokenName, $abilities, $expiresAt);

            // Mise à jour de la dernière connexion
            $user->updateLastLogin();

            return response()->json([
                'message' => 'Inscription réussie',
                'user' => $this->formatUserResponse($user),
                'access_token' => $token->plainTextToken,
                'token_type' => 'Bearer',
                'expires_at' => $token->accessToken->expires_at,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de l\'inscription',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    // ===== CONNEXION =====
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        try {
            // Validation des données
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'email'],
                'password' => ['required', 'string'],
                'remember' => ['boolean'],
                'device_name' => ['nullable', 'string', 'max:255'],
                'revoke_other_tokens' => ['boolean'],
            ], [
                'email.required' => 'L\'adresse email est obligatoire.',
                'email.email' => 'L\'adresse email doit être valide.',
                'password.required' => 'Le mot de passe est obligatoire.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Rate limiting - 5 tentatives par minute
            $key = 'login.' . $request->ip();

            if (RateLimiter::tooManyAttempts($key, 5)) {
                $seconds = RateLimiter::availableIn($key);

                return response()->json([
                    'message' => 'Trop de tentatives de connexion. Réessayez dans ' . $seconds . ' secondes.',
                ], 429);
            }

            // Vérification des identifiants
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                RateLimiter::hit($key);

                return response()->json([
                    'message' => 'Les identifiants fournis sont incorrects.',
                    'errors' => [
                        'email' => ['Les identifiants fournis sont incorrects.']
                    ]
                ], 401);
            }

            // Vérification du statut du compte
            if (!$user->is_active) {
                return response()->json([
                    'message' => 'Votre compte est désactivé. Contactez l\'administrateur.',
                ], 403);
            }

            // Révoquer tous les tokens existants si demandé
            if ($request->revoke_other_tokens) {
                $user->tokens()->delete();
            }

            // Créer un nouveau token
            $tokenName = $request->device_name ?? 'login_token';
            $abilities = $this->getTokenAbilities($user->role);
            $expiresAt = $request->remember ? now()->addDays(30) : now()->addHours(24);

            $token = $user->createToken($tokenName, $abilities, $expiresAt);

            // Mettre à jour la dernière connexion
            $user->updateLastLogin();

            // Effacer le rate limiting en cas de succès
            RateLimiter::clear($key);

            return response()->json([
                'message' => 'Connexion réussie',
                'user' => $this->formatUserResponse($user),
                'access_token' => $token->plainTextToken,
                'token_type' => 'Bearer',
                'expires_at' => $token->accessToken->expires_at,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la connexion',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    // ===== DÉCONNEXION =====
    public function logout(Request $request): JsonResponse
    {
        try {
            // Supprimer le token actuel
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Déconnexion réussie'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la déconnexion',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    // ===== DÉCONNEXION DE TOUS LES APPAREILS =====
    public function logoutAll(Request $request): JsonResponse
    {
        try {
            // Supprimer tous les tokens de l'utilisateur
            $tokensDeleted = $request->user()->tokens()->count();
            $request->user()->tokens()->delete();

            return response()->json([
                'message' => 'Déconnexion de tous les appareils réussie',
                'tokens_deleted' => $tokensDeleted
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la déconnexion',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    // ===== PROFIL UTILISATEUR =====
    public function me(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $currentToken = $user->currentAccessToken();

            return response()->json([
                'user' => $this->formatUserResponse($user),
                'token_info' => [
                    'name' => $currentToken->name,
                    'abilities' => $currentToken->abilities,
                    'expires_at' => $currentToken->expires_at,
                    'created_at' => $currentToken->created_at,
                    'last_used_at' => $currentToken->last_used_at,
                ],
                'active_tokens' => $user->tokens()->count(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération du profil',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    // ===== RAFRAÎCHIR LE TOKEN =====
    public function refreshToken(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $currentToken = $user->currentAccessToken();

            // Créer un nouveau token avec les mêmes capacités
            $newToken = $user->createToken(
                $currentToken->name,
                $currentToken->abilities,
                now()->addHours(24)
            );

            // Supprimer l'ancien token
            $currentToken->delete();

            return response()->json([
                'message' => 'Token rafraîchi avec succès',
                'access_token' => $newToken->plainTextToken,
                'token_type' => 'Bearer',
                'expires_at' => $newToken->accessToken->expires_at,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors du rafraîchissement du token',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    // ===== CHANGER LE MOT DE PASSE =====
    public function changePassword(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => ['required', 'string'],
                'password' => ['required', 'confirmed', Password::defaults()],
            ], [
                'current_password.required' => 'Le mot de passe actuel est obligatoire.',
                'password.required' => 'Le nouveau mot de passe est obligatoire.',
                'password.confirmed' => 'La confirmation du nouveau mot de passe ne correspond pas.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();

            // Vérifier l'ancien mot de passe
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'message' => 'Le mot de passe actuel est incorrect.',
                    'errors' => [
                        'current_password' => ['Le mot de passe actuel est incorrect.']
                    ]
                ], 422);
            }

            // Mettre à jour le mot de passe
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            // Révoquer tous les autres tokens (optionnel)
            if ($request->revoke_other_tokens) {
                $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();
            }

            return response()->json([
                'message' => 'Mot de passe modifié avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la modification du mot de passe',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    // ===== METTRE À JOUR LE PROFIL =====
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $validator = Validator::make($request->all(), [
                'nom' => ['sometimes', 'required', 'string', 'max:100'],
                'prenom' => ['sometimes', 'required', 'string', 'max:100'],
                'email' => ['sometimes', 'required', 'email', 'unique:users,email,' . $user->id],
                'telephone' => ['nullable', 'string', 'max:20'],
            ], [
                'nom.required' => 'Le nom est obligatoire.',
                'prenom.required' => 'Le prénom est obligatoire.',
                'email.required' => 'L\'adresse email est obligatoire.',
                'email.unique' => 'Cette adresse email est déjà utilisée.',
                'email.email' => 'L\'adresse email doit être valide.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user->update($request->only(['nom', 'prenom', 'email', 'telephone']));

            return response()->json([
                'message' => 'Profil mis à jour avec succès',
                'user' => $this->formatUserResponse($user->fresh())
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la mise à jour du profil',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    // ===== LISTER LES TOKENS ACTIFS =====
    public function tokens(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $tokens = $user->tokens()->get()->map(function ($token) {
                return [
                    'id' => $token->id,
                    'name' => $token->name,
                    'abilities' => $token->abilities,
                    'created_at' => $token->created_at,
                    'last_used_at' => $token->last_used_at,
                    'expires_at' => $token->expires_at,
                    'is_current' => $token->id === $user->currentAccessToken()->id,
                ];
            });

            return response()->json([
                'tokens' => $tokens,
                'total' => $tokens->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des tokens',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    // ===== RÉVOQUER UN TOKEN SPÉCIFIQUE =====
    public function revokeToken(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'token_id' => ['required', 'integer'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();
            $token = $user->tokens()->find($request->token_id);

            if (!$token) {
                return response()->json([
                    'message' => 'Token introuvable'
                ], 404);
            }

            $tokenName = $token->name;
            $token->delete();

            return response()->json([
                'message' => 'Token "' . $tokenName . '" révoqué avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la révocation du token',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    // ===== VÉRIFIER L'ÉTAT DU TOKEN =====
    public function checkToken(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $token = $user->currentAccessToken();

            return response()->json([
                'valid' => true,
                'user' => $this->formatUserResponse($user),
                'token' => [
                    'name' => $token->name,
                    'abilities' => $token->abilities,
                    'expires_at' => $token->expires_at,
                    'expires_in_hours' => $token->expires_at ? $token->expires_at->diffInHours(now()) : null,
                    'is_expired' => $token->expires_at ? $token->expires_at->isPast() : false,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'message' => 'Token invalide ou expiré'
            ], 401);
        }
    }

    // ===== MÉTHODES PRIVÉES =====

    /**
     * Formater la réponse utilisateur
     */
    private function formatUserResponse(User $user): array
    {
        return [
            'id' => $user->id,
            'nom' => $user->nom,
            'prenom' => $user->prenom,
            'email' => $user->email,
            'telephone' => $user->telephone,
            'role' => $user->role,
            'full_name' => $user->prenom . ' ' . $user->nom,
            'is_active' => $user->is_active,
            'last_login_at' => $user->last_login_at,
            'email_verified_at' => $user->email_verified_at,
            'created_at' => $user->created_at,
        ];
    }

    /**
     * Obtenir les capacités du token selon le rôle
     */
    private function getTokenAbilities(string $role): array
    {
        return match ($role) {
            'admin' => ['*'],
            'gestionnaire' => [
                'membres:read',
                'membres:write',
                'cotisations:read',
                'cotisations:write',
                'credits:read',
                'credits:write',
                'assistances:read',
                'assistances:write',
                'rapports:read',
                'rapports:write',
            ],
            'membre' => [
                'profile:read',
                'profile:write',
                'cotisations:read',
                'credits:read',
                'credits:request',
                'assistances:read',
                'assistances:request',
            ],
            default => ['profile:read'],
        };
    }
}


