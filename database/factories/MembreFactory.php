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
        return [
            'user_id' => User::factory(),
            'matricule' => fake()->regexify('[A-Za-z0-9]{50}'),
            'nom' => fake()->regexify('[A-Za-z0-9]{100}'),
            'prenom' => fake()->regexify('[A-Za-z0-9]{100}'),
            'email' => fake()->safeEmail(),
            'telephone' => fake()->regexify('[A-Za-z0-9]{20}'),
            'categorie_id' => CategorieMembre::factory(),
            'statut' => fake()->randomElement(["actif","inactif","suspendu"]),
            'date_adhesion' => fake()->date(),
        ];
    }
}
