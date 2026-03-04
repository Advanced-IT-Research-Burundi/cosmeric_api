<?php

namespace App\Services;

use App\Models\User;
use App\Models\Membre;
use App\Models\Credit;
use App\Models\Remboursement;
use App\Models\Cotisation;
use App\Models\Configuration;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportationService
{
    /**
     * Process the imported data from Excel.
     *
     * @param array $cotisations Array of cotisation data from request
     * @param string $date Date of the import (Y-m format)
     * @return void
     */
    public function processImport(array $cotisations, string $date)
    {
        foreach ($cotisations as $data) {
            if (empty($data['matricule']) || !is_numeric($data['matricule'])) {
                continue;
            }

            DB::transaction(function () use ($data, $date) {
                $membre = $this->resolveMembre($data);
                
                $retenu = floatval($data['retenu'] ?? 0);
                
                if ($retenu > 0) {
                    $this->handleCreditAndRemboursement($membre, $data, $retenu, $date);
                }

                // Handle Cotisation if it's a cotisation type or based on some other logic
                // The original logic was creating CotisationMensuelle staging records, 
                // but we should probably also record actual Cotisations for the member.
                // For now, let's focus on the User/Credit/Remboursement requirements.
            });
        }
    }

    /**
     * Resolve member from matricule, create user and member if not exists.
     */
    private function resolveMembre(array $data)
    {
        $matricule = $data['matricule'];
        $membre = Membre::where('matricule', $matricule)->first();

        if (!$membre) {
            // Create User
            $user = User::create([
                'name' => $data['name'] ?? 'Imported User',
                'nom' => $data['name'] ?? 'Imported',
                'prenom' => 'User',
                'email' => $matricule . '@cosmeric.com', // Placeholder email
                'password' => Hash::make($matricule), // Password is the matricule
                'role' => 'membre',
                'is_active' => false,
            ]);

            // Create Membre
            $membre = Membre::create([
                'user_id' => $user->id,
                'matricule' => $matricule,
                'nom' => $data['name'] ?? 'Imported',
                'prenom' => 'User',
                'categorie_id' => 3, // Default category
                'statut' => 'actif',
                'date_adhesion' => now(),
            ]);
        }

        return $membre;
    }

    /**
     * Handle credit creation and reimbursement.
     */
    private function handleCreditAndRemboursement(Membre $membre, array $data, float $retenu, string $date)
    {
        // Find an active credit
        $credit = Credit::where('membre_id', $membre->id)
            ->where('statut', 'approuve')
            ->where('montant_restant', '>', 0)
            ->first();

        if (!$credit) {
            $defaultTaux = Configuration::where('cle', 'taux_interet_credit')->value('valeur') ?? 3;
            $globalAmount = floatval($data['global'] ?? 0);

            $credit = Credit::create([
                'membre_id' => $membre->id,
                'montant_demande' => $globalAmount,
                'montant_accorde' => $globalAmount,
                'taux_interet' => $defaultTaux,
                'duree_mois' => 12,
                'montant_total_rembourser' => $globalAmount,
                'montant_mensualite' => floatval($data['retenu'] ?? 0), // Use current retenu as suggested monthly
                'date_demande' => now(),
                'date_approbation' => now(),
                'date_fin' => now()->addMonths(12),
                'statut' => 'approuve',
                'user_id' => auth()->id() ?? $membre->user_id,
                'montant_restant' => $globalAmount,
            ]);
        }

        // Create Remboursement
        Remboursement::create([
            'credit_id' => $credit->id,
            'numero_echeance' => $credit->remboursements()->count() + 1,
            'montant_prevu' => $retenu,
            'montant_paye' => $retenu,
            'date_echeance' => Carbon::parse($date)->day(now()->day),
            'date_paiement' => Carbon::parse($date)->day(now()->day),
            'statut' => 'paye',
            'penalite' => 0,
        ]);

        // Update credit remaining amount
        $credit->decrement('montant_restant', $retenu);
    }
}
