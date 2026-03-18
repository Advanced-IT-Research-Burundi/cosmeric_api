<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Notification;

class NotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Notification::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'type' => fake()->word(),
            'title' => fake()->sentence(4),
            'message' => fake()->text(),
            'time' => fake()->dateTime(),
            'read' => fake()->boolean(),
            'user_id' => fake()->numberBetween(-10000, 10000),
        ];
    }
}
