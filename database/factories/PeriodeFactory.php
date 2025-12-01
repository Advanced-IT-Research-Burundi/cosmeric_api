<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Periode;

class PeriodeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Periode::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(["mensuel", "semestriel"]),
            'mois' => fake()->numberBetween(-10000, 10000),
            'semestre' => fake()->numberBetween(-10000, 10000),
            'annee' => fake()->year(),
            'statut' => fake()->randomElement(["ouvert", "ferme"]),
            'date_debut' => fake()->date(),
            'date_fin' => fake()->date(),
        ];
    }
}
