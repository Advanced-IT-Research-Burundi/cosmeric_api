<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Credit;
use App\Models\Membre;
use App\Models\User;

class CreditFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Credit::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'membre_id' => Membre::factory(),
            'montant_demande' => fake()->randomFloat(2, 0, 99999999.99),
            'montant_accorde' => fake()->randomFloat(2, 0, 99999999.99),
            'taux_interet' => fake()->randomFloat(2, 0, 999.99),
            'duree_mois' => fake()->numberBetween(-10000, 10000),
            'montant_total_rembourser' => fake()->randomFloat(2, 0, 99999999.99),
            'montant_mensualite' => fake()->randomFloat(2, 0, 99999999.99),
            'date_demande' => fake()->date(),
            'date_approbation' => fake()->date(),
            'statut' => fake()->randomElement(["en_attente","approuve","rejete","en_cours","termine"]),
            'motif' => fake()->text(),
            'user_id' => User::factory(),
        ];
    }
}
