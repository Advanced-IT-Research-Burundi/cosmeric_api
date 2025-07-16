<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Credit;
use App\Models\Remboursement;

class RemboursementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Remboursement::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'credit_id' => Credit::factory(),
            'numero_echeance' => fake()->numberBetween(-10000, 10000),
            'montant_prevu' => fake()->randomFloat(2, 0, 99999999.99),
            'montant_paye' => fake()->randomFloat(2, 0, 99999999.99),
            'date_echeance' => fake()->date(),
            'date_paiement' => fake()->date(),
            'statut' => fake()->randomElement(["prevu","paye","en_retard"]),
            'penalite' => fake()->randomFloat(2, 0, 99999999.99),
        ];
    }
}
