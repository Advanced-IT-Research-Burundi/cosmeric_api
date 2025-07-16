<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Configuration;

class ConfigurationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Configuration::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'cle' => fake()->regexify('[A-Za-z0-9]{100}'),
            'valeur' => fake()->text(),
            'description' => fake()->text(),
        ];
    }
}
