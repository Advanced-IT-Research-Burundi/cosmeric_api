<?php

namespace App\Console\Commands;

use App\Models\Configuration;
use Illuminate\Console\Command;

class conf extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:conf';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize application configurations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Configuration::insert([
            [
                'cle' => 'taux_change_usd_bif',
                'valeur' => '3',
                'description' => 'Taux de change entre USD et BIF',
            ],
            [
                'cle' => 'duree_minimale_en_mois',
                'valeur' => '3',
                'description' => 'Durée minimale',
            ],
            [
                'cle' => 'duree_maximale_en_mois',
                'valeur' => '24',
                'description' => 'Durée maximale',
            ],
            [
                'cle' => 'montant_minimal_cotisation',
                'valeur' => '500000',
                'description' => 'Montant minimum en FBU',
            ],
            [
                'cle' => 'montant_maximal_cotisation',
                'valeur' => '10000000',
                'description' => 'Montant maximum en FBU',
            ],
        ]);

        $this->info('Configurations créées avec succès.');
    }
}
