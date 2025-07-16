<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\CategorieMembre;

class CategorieMembreFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CategorieMembre::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'nom' => fake()->regexify('[A-Za-z0-9]{100}'),
            'montant_cotisation' => fake()->randomFloat(2, 0, 99999999.99),
            'devise' => fake()->randomElement(["FBU","USD"]),
            'frequence_paiement' => fake()->randomElement(["mensuel","semestriel"]),
            'description' => fake()->text(),
        ];
    }
}
