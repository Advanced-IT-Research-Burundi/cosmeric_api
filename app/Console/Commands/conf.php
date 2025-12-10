<?php

namespace App\Console\Commands;

use App\Models\Configuration;
use App\Models\Cotisation;
use App\Models\CotisationMensuelle;
use App\Models\TypeAssistance;
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
        TypeAssistance::insert([
            [
                'nom' => 'Mariage',
                'montant_standard' => '300000',
            ],
            [
                'nom' => 'Retraite',
                'montant_standard' => '500000',
            ],
            [

                'nom' => 'Decés',
                'montant_standard' => '700000',
            ]
        ]);

        // CotisationMensuelle::factory()->count(5)->create();
        // Cotisation::factory()->count(5)->create();

        $this->info('Configurations créées avec succès.');
    }
}
