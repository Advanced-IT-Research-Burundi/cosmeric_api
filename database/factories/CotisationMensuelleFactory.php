<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\CotisationMensuelle;
use App\Models\Membre;

class CotisationMensuelleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CotisationMensuelle::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'matricule' => 1,
            'nomero_dossier' => fake()->word(),
            'global' => fake()->word(),
            'regle' => fake()->word(),
            'restant' => fake()->word(),
            'retenu' => fake()->word(),
            'date_cotisation' => fake()->word(),
            'user_id' => 1,
        ];
    }
}
