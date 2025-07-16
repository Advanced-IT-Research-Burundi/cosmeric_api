<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Membre;
use App\Models\Transaction;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'membre_id' => Membre::factory(),
            'type_transaction' => fake()->randomElement(["cotisation","credit","remboursement","assistance"]),
            'reference_transaction' => fake()->numberBetween(-10000, 10000),
            'montant' => fake()->randomFloat(2, 0, 99999999.99),
            'devise' => fake()->randomElement(["FBU","USD"]),
            'sens' => fake()->randomElement(["entree","sortie"]),
            'date_transaction' => fake()->date(),
            'description' => fake()->text(),
            'created_at' => fake()->dateTime(),
        ];
    }
}
