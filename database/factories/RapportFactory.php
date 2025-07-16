<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\GenerePar;
use App\Models\Rapport;

class RapportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Rapport::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'titre' => fake()->regexify('[A-Za-z0-9]{200}'),
            'type_rapport' => fake()->randomElement(["mensuel","semestriel","annuel","personnalise"]),
            'periode_debut' => fake()->date(),
            'periode_fin' => fake()->date(),
            'genere_par' => GenerePar::factory(),
            'fichier_path' => fake()->regexify('[A-Za-z0-9]{255}'),
            'statut' => fake()->randomElement(["genere","envoye","archive"]),
            'created_at' => fake()->dateTime(),
        ];
    }
}
