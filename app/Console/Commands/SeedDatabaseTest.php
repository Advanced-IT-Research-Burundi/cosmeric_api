<?php

namespace App\Console\Commands;

use App\Models\Cotisation;
use App\Models\Periode;
use Illuminate\Console\Command;

class SeedDatabaseTest extends Command
{
    /**
    * The name and signature of the console command.
    *
    * @var string
    */
    protected $signature = 'app:seed-database-test';
    
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
        $this->populate();
        
        $this->info("finish");
    }
    
    
    public function populate(){
        for($i = 0 ; $i < 15 ; $i++){
            Periode::create(
                [
                 'mois' => random_int(1,12),
                 'annee'=> random_int(2000,2060),
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
