<?php

namespace App\Console\Commands;

use App\Models\Assistance;
use App\Models\Cotisation;
use App\Models\CotisationMensuelle;
use App\Models\Credit;
use App\Models\Periode;
use App\Models\Transaction;
use App\Models\TypeAssistance;
use Illuminate\Console\Command;

class SeedDatabaseTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $this->info('Seeding database for testing purposes...');
        // $this->populate();

        Assistance::factory(10)->create();
        $this->info("finish");
    }

    public function populateByModel(Transaction $name)
    {
        $name::factory(10)->create();
    }


    public function populate()
    {
        for ($i = 0; $i < 15; $i++) {
            TypeAssistance::create(
                [
                    'nom' => 'Type d\'assistance ' . $i,
                    'montant_standard' => random_int(1000, 10000),
                    'conditions' => 'Conditions de l\'assistance ' . $i,
                    'documents_requis' => 'Documents requis pour l\'assistance ' . $i,
                ]
            );
            Assistance::create(
                [
                    "membre_id" => 1,
                    "type_assistance_id" => 1,
                    "montant" => random_int(1000, 100000),
                    "date_demande" => now(),
                    "date_approbation" => now(),
                    "date_versement" => now(),
                    "statut" => "en_attente",
                    "justificatif" => "string",
                    "motif_rejet" => "string"
                ]
            );

            Credit::create(
                [
                    "membre_id" => 1,
                    "montant_demande" => random_int(1000, 100000),
                    "montant_accorde" => random_int(1000, 100000),
                    "taux_interet" => random_int(1, 10),
                    "duree_mois" => random_int(1, 24),
                    "montant_total_rembourser" => 0,
                    "montant_mensualite" => 0,
                    "date_demande" => now(),
                    "date_approbation" => now(),
                    "statut" => "en_attente",
                    "motif" => "string"
                ]
            );

            Periode::create(
                [
                    'mois' => random_int(1, 12),
                    'annee' => random_int(2000, 2060),
                    'statut' => 'ouvert',
                    'date_debut' => date('Y-m-d'),
                    'date_fin' =>  date('Y-m-d'),
                ]
            );
            Cotisation::create([
                'membre_id' => 1,
                'periode_id' => 1,
                'montant' => 1500,
                'devise' => 'FBU',
                'date_paiement' => now(),
                'statut' => 1,
                'mode_paiement' => 1,
                'reference_paiement' => time(),
            ]);
        }
    }
}
