<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\TypeAssistance;

class TypeAssistanceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TypeAssistance::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'nom' => fake()->word(),
            'montant_standard' => fake()->randomFloat(2, 0, 99999.99),
            'conditions' => fake()->text(),
            'documents_requis' => fake()->text(),
        ];
    }
}
