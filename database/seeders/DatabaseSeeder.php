<?php

namespace Database\Seeders;

use App\Models\Assistance;
use App\Models\CategorieMembre;
use App\Models\Configuration;
use App\Models\Cotisation;
use App\Models\Credit;
use App\Models\Membre;
use App\Models\TypeAssistance;
use App\Models\User;
use Database\Factories\AssistanceFactory;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'nom' => 'Jean',
            'prenom' => 'Lionel',
            'email' => 'nijeanlionel@gmail.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $typesassistances = ['mariage', 'retraite', 'deces'];

        foreach ($typesassistances as $key => $value) {
        }

        $categorieMembres = [
            [
                'nom' => 'cadre_contractuel',
                'montant_cotisation' => 4000,
                'devise' => "FBU",
                'frequence_paiement' => "mensuel",
                'description' => 'Cadre Contractuel ou non',
            ],
            [
                'nom' => 'collaborateur_a2',
                'montant_cotisation' => 2000,
                'devise' => "FBU",
                'frequence_paiement' => "mensuel",
                'description' => 'Collaborateur (Niveau A2)',
            ],
            [
                'nom' => 'chauffeur_planton',
                'montant_cotisation' => 1000,
                'devise' => "FBU",
                'frequence_paiement' => "mensuel",
                'description' => 'Chauffeur / Planton',
            ],
            [
                'nom' => 'service_externe_10',
                'montant_cotisation' => 10,
                'devise' => "USD",
                'frequence_paiement' => "mensuel",
                'description' => 'Service Externe (Cadres)',
            ],
            [
                'nom' => 'service_externe_5',
                'montant_cotisation' => 5,
                'devise' => "USD",
                'frequence_paiement' => "mensuel",
                'description' => 'Service Externe (Secrétaires d\’ambassade)',
            ],
        ];

        foreach ($categorieMembres as $categorie) {
            CategorieMembre::factory()->create($categorie);
        }

        for ($i = 0; $i < 20; $i++) {
            Membre::factory()->create();
            // Assistance::factory()->create([
            //     'membre_id' => $i,
            //     'type_assistance_id' => 1,
            //     'montant' => fake()->randomFloat(2, 100, 1000),
            //     'date_demande' => fake()->date(),
            //     'date_approbation' => fake()->date(),
            //     'date_versement' => fake()->date(),
            //     'statut' => fake()->randomElement(["en_attente","approuve","rejete","verse"]),
            //     'justificatif' => fake()->regexify('[A-Za-z0-9]{255}'),
            //     'motif_rejet' => fake()->regexify('[A-Za-z0-9]{255}'),
            // ]);
        }


            $typesAssistances = [[ 'mariage', 500000], ['retraite', 400000], ['deces', 300000]];

    foreach ($typesAssistances as $type) {
        TypeAssistance::firstOrCreate([
            'nom' => $type[0],
            'montant_standard' => $type[1],
            'conditions' => 'Conditions pour ' . $type[0],
            'documents_requis' => 'Documents requis pour ' . $type[0],
        ]);
    }

        // Assistance::factory()->count(10)->create();
        // Cotisation::factory()->count(10)->create();
        // Credit::factory()->count(10)->create();

    }
}
