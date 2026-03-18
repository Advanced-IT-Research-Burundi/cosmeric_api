<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Categorie;
use App\Models\CategorieMembre;
use App\Models\Membre;
use App\Models\User;

class MembreFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Membre::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $year = now()->year; // ou faker()->year()
        $sequence = str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT);

        return [
            'user_id' => User::factory(),
            'matricule' => "M-{$year}-{$sequence}",
            'nom' => fake()->firstName(),
            'prenom' => fake()->lastName(),
            'email' => fake()->safeEmail(),
            'telephone' => fake()->phoneNumber(),
            'categorie_id' => fake()->randomElement(CategorieMembre::pluck('id')->toArray()),
            'statut' => fake()->randomElement(["actif","inactif","suspendu"]),
            'date_adhesion' => fake()->date(),
        ];
    }
}
