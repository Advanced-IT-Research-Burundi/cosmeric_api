<?php

namespace App\Services;

use App\Models\User;
use App\Models\Membre;
use App\Models\Credit;
use App\Models\Remboursement;
use App\Models\Cotisation;
use App\Models\CotisationMensuelle;
use App\Models\Configuration;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportationService
{
    /**
     * Process the imported data from staging table (CotisationMensuelle).
     * Moves data from staging to permanent tables: Cotisation, Credit, Remboursement.
     *
     * @param array $stagingRecords Array of CotisationMensuelle staging records
     * @param string $date Date of the import (Y-m format)
     * @return void
     * @throws \Exception
     */
    public function processImport(array $stagingRecords, string $date)
    {


        foreach ($stagingRecords as $data) {
            if (empty($data['matricule']) || !is_numeric($data['matricule'])) {
                continue;
            }

            // Resolve or create member
            $membre = $this->resolveMembre($data);

            // Handle reimbursement (retenu)
            $retenu = floatval($data['retenu'] ?? 0);
            if ($retenu > 0) {
                $this->handleCreditAndRemboursement($membre, $data, $retenu, $date);
            }

            // Create cotisation record from staging data
            $this->processCotisation($membre, $data, $date);
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


            if ($data['retenu'] == 4000) {
                $category = 1;
            } else if ($data['retenu'] == 2000) {
                $category = 2;
            } else if ($data['retenu'] == 1000) {
                $category = 3;
            } else {
                $category = 3; // Default category
            }

            $nom = explode(" ", $data['name']);
            $prenom = $nom[0];
            $nom = $nom[1];


            // Create Membre
            $membre = Membre::create([
                'user_id' => $user->id,
                'matricule' => $matricule,
                'nom' => $nom,
                'email' => strtolower($nom.$prenom). '@cosmeric.com',
                'telephone' => "+257 00000000",
                'prenom' => $prenom,
                'categorie_id' => $category, // Default category
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
         $credit = Credit::where('membre_id', $membre->id)
        ->where('statut', 'approuve')
        ->where('montant_restant', '>', 0)
        ->lockForUpdate()
        ->first();

    if (!$credit) {
        throw new Exception("No active credit found.");
    }

    if ($retenu > $credit->montant_restant) {
        throw new Exception("Payment exceeds remaining balance.");
    }

        //sort the type of input if it is a reimbursement or a cotisation
        $type = "COTISATION";
        if (!empty($data['global']) || !empty($data['restant'])) {
            $type = "REMBOURSEMENT";
        }

        // If type is cotisation do not add it in credit and remboursement tables
        if ($type === "COTISATION") {
            return;
        }

        $credit = Credit::where('membre_id', $membre->id)
            ->where('statut', 'approuve')
            ->where('montant_restant', '>', 0)
            ->lockForUpdate()
            ->first();

        if ($credit) {
            $this->handleRemboursement($credit, $retenu, $date);
        } else {
            $globalAmount = floatval($data['global'] ?? 0);
            if ($globalAmount > 0) {
                $this->createCreditRequest($membre, $data, $globalAmount);
            }
        }
    }

    /**
     * Create a new credit request in 'en_attente' status.
     */
    private function createCreditRequest(Membre $membre, array $data, float $amount)
    {
        $defaultTaux = Configuration::where('cle', 'taux_interet_credit')->value('valeur') ?? 3;

        return Credit::create([
                'membre_id' => $membre->id,
            'montant_demande' => $amount,
            'montant_accorde' => 0,
                'taux_interet' => $defaultTaux,
                'duree_mois' => 12,
            'montant_total_rembourser' => 0,
            'montant_mensualite' => 0,
                'date_demande' => now(),
            'statut' => 'approuve',
                'created_by' => auth()->id() ?? $membre->user_id,
                'user_id' => auth()->id() ?? $membre->user_id,
            'montant_restant' => 0,
            ]);
        }

    /**
     * Record a reimbursement and update credit balance.
     */
    private function handleRemboursement(Credit $credit, float $amount, string $date)
    {
        // Ensure we don't overpay
        $paymentAmount = min($amount, $credit->montant_restant);

        Remboursement::create([
            'credit_id' => $credit->id,
            'numero_echeance' => $credit->remboursements()->count() + 1,
            'montant_prevu' => $paymentAmount,
            'montant_paye' => $paymentAmount,
            'date_echeance' => Carbon::parse($date)->day(now()->day),
            'date_paiement' => Carbon::parse($date)->day(now()->day),
            'statut' => 'paye',
            'penalite' => 0,
        ]);

        $credit->decrement('montant_restant', $paymentAmount);
    }

    /**
     * Process cotisation: move data from staging to permanent Cotisation table.
     */
    private function processCotisation(Membre $membre, array $stagingData, string $date)
    {
        $montantRestant = floatval($stagingData['restant'] ?? 0);

        CotisationMensuelle::create([
            'membre_id' => $membre->id,
            'montant_regle' => floatval($stagingData['regle'] ?? 0),
            'montant_restant' => $montantRestant,
            'montant_global' => floatval($stagingData['global'] ?? 0),
            'montant_retenu' => floatval($stagingData['retenu'] ?? 0),
            'date_cotisation' => Carbon::parse($date)->endOfMonth(),
            'statut' => $montantRestant > 0 ? 'partiel' : 'paye',
        ]);

        Cotisation::create([
            'membre_id' => $membre->id,
            'montant' => floatval($stagingData['retenu'] ?? 0),
            'date_cotisation' => Carbon::parse($date)->endOfMonth(),
            'statut' => 'paye',
            'mode_paiement' => 'Banque',
            'date_paiement' => Carbon::parse($date)->endOfMonth(),
            'reference_paiement' => 'Importation - ' . Carbon::now()->timestamp,
        ]);
    }
}
