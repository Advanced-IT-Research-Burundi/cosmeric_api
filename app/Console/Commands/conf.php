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
                'valeur' => '300000',
                'description' => 'Mariage',
            ],
            [
                'valeur' => '500000',
                'description' => 'Retraite',
            ],
            [

                'valeur' => '700000',
                'description' => 'Deces',
            ]
        ]);

        $this->info('Configurations créées avec succès.');
    }
}
