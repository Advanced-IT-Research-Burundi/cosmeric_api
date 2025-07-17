<?php

namespace Database\Seeders;

use App\Models\Assistance;
use App\Models\CategorieMembre;
use App\Models\Membre;
use App\Models\User;
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

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'nom' => 'Jean',
        //     'prenom' => 'Lionel',
        //     'email' => 'nijeanlionel@gmail.com',
        //     'password' => Hash::make('password'),
        //     'is_active' => true,
        // ]);

        for ($i = 0; $i < 20; $i++) {
            CategorieMembre::factory()->create([
                'nom' => fake()->regexify('[A-Za-z0-9]{100}'),
            ]);
            Membre::factory()->create([
                'user_id' => 1,
                'matricule' => fake()->regexify('[A-Za-z0-9]{50}'),
                'nom' => fake()->regexify('[A-Za-z0-9]{100}'),
                'prenom' => fake()->regexify('[A-Za-z0-9]{100}'),
                'email' => fake()->safeEmail(),
                'telephone' => fake()->regexify('[A-Za-z0-9]{20}'),
                'categorie_id' => 1,
                'statut' => fake()->randomElement(["actif","inactif","suspendu"]),
                'date_adhesion' => fake()->date(),
            ]);
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
    }
}
