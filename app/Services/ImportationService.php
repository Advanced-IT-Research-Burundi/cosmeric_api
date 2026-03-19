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
     * Sépare les entrées COTISATION et REMBOURSEMENT.
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

            // Déterminer le type: REMBOURSEMENT si global ou restant est présent
            $type = "COTISATION";
            if (!empty($data['global']) || !empty($data['restant'])) {
                $type = "REMBOURSEMENT";
            }

            if ($type === "COTISATION") {
                // Pour les cotisations, on essaie de trouver le membre mais ce n'est pas obligatoire
                $membre = Membre::where('matricule', $data['matricule'])->first();
                $this->processCotisation($membre, $data, $date);
            } else {
                // Pour les remboursements, on doit avoir un membre
                $membre = $this->resolveMembre($data);
                $retenu = floatval($data['retenu'] ?? 0);
                if ($retenu > 0) {
                    $this->processRemboursement($membre, $data, $retenu, $date);
                }
            }
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
     * Inclut toutes les données d'importation.
     */
    private function handleRemboursement(Credit $credit, array $data, float $amount, string $date)
    {
        // Ensure we don't overpay
        $paymentAmount = min($amount, $credit->montant_restant);

        // Extraire nom et prénom du champ name
        $nameParts = $this->extractNomPrenom($data['name'] ?? '');

        Remboursement::create([
            'credit_id' => $credit->id,
            'matricule' => $data['matricule'] ?? null,
            'nom' => $nameParts['nom'],
            'prenom' => $nameParts['prenom'],
            'nomero_dossier' => $data['nomero_dossier'] ?? null,
            'global' => $data['global'] ?? null,
            'regle' => $data['regle'] ?? null,
            'restant' => $data['restant'] ?? null,
            'retenu' => $data['retenu'] ?? null,
            'numero_echeance' => $credit->remboursements()->count() + 1,
            'montant_prevu' => $paymentAmount,
            'montant_paye' => $paymentAmount,
            'date_echeance' => Carbon::parse($date)->day(now()->day),
            'date_paiement' => Carbon::parse($date)->day(now()->day),
            'statut' => 'paye',
            'penalite' => 0,
            'is_import' => true,
        ]);

        $credit->decrement('montant_restant', $paymentAmount);
    }

    /**
     * Extrait nom et prénom d'une chaîne "Prénom Nom".
     */
    private function extractNomPrenom(string $fullName): array
    {
        $parts = explode(' ', trim($fullName), 2);
        return [
            'prenom' => $parts[0] ?? '',
            'nom' => $parts[1] ?? '',
        ];
    }

    /**
     * Process cotisation: move data from staging to permanent Cotisation table.
     * Membre peut être null, dans ce cas on utilise juste le matricule.
     * Inclut toutes les données d'importation.
     */
    private function processCotisation(?Membre $membre, array $stagingData, string $date)
    {
        // Extraire nom et prénom du champ name
        $nameParts = $this->extractNomPrenom($stagingData['name'] ?? '');

        Cotisation::create([
            'membre_id' => $membre?->id,
            'matricule' => $stagingData['matricule'],
            'nom' => $nameParts['nom'],
            'prenom' => $nameParts['prenom'],
            'nomero_dossier' => $stagingData['nomero_dossier'] ?? null,
            'global' => $stagingData['global'] ?? null,
            'regle' => $stagingData['regle'] ?? null,
            'restant' => $stagingData['restant'] ?? null,
            'retenu' => $stagingData['retenu'] ?? null,
            'montant' => floatval($stagingData['retenu'] ?? 0),
            'date_paiement' => Carbon::parse($date)->endOfMonth(),
            'statut' => 'paye',
            'mode_paiement' => 'Banque',
            'reference_paiement' => 'Importation - ' . Carbon::now()->timestamp,
            'is_import' => true,
        ]);
    }

    /**
     * Process remboursement: gère le crédit et crée le remboursement.
     */
    private function processRemboursement(Membre $membre, array $data, float $retenu, string $date)
    {
        $credit = Credit::where('membre_id', $membre->id)
            ->where('statut', 'approuve')
            ->where('montant_restant', '>', 0)
            ->lockForUpdate()
            ->first();

        if ($credit) {
            // Si un crédit actif existe, on enregistre le remboursement
            $this->handleRemboursement($credit, $data, $retenu, $date);
        } else {
            // Sinon, on crée une demande de crédit basée sur le montant global
            $globalAmount = floatval($data['global'] ?? 0);
            if ($globalAmount > 0) {
                $this->createCreditRequest($membre, $data, $globalAmount);
            }
        }
    }
}
