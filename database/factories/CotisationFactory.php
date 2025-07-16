<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Cotisation;
use App\Models\Membre;
use App\Models\Periode;

class CotisationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Cotisation::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'membre_id' => Membre::factory(),
            'periode_id' => Periode::factory(),
            'montant' => fake()->randomFloat(2, 0, 99999999.99),
            'devise' => fake()->randomElement(["FBU","USD"]),
            'date_paiement' => fake()->date(),
            'statut' => fake()->randomElement(["paye","en_attente","en_retard"]),
            'mode_paiement' => fake()->regexify('[A-Za-z0-9]{50}'),
            'reference_paiement' => fake()->regexify('[A-Za-z0-9]{100}'),
        ];
    }
}
