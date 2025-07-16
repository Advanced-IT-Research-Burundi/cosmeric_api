<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Assistance;
use App\Models\Membre;
use App\Models\TypeAssistance;

class AssistanceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Assistance::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'membre_id' => Membre::factory(),
            'type_assistance_id' => TypeAssistance::factory(),
            'montant' => fake()->randomFloat(2, 0, 99999999.99),
            'date_demande' => fake()->date(),
            'date_approbation' => fake()->date(),
            'date_versement' => fake()->date(),
            'statut' => fake()->randomElement(["en_attente","approuve","rejete","verse"]),
            'justificatif' => fake()->regexify('[A-Za-z0-9]{255}'),
            'motif_rejet' => fake()->text(),
        ];
    }
}
