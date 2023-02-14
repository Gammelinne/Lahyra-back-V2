<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->name,
            'body' => $this->faker->sentence(10),
            'type' => 'test',
            'url' => $this->faker->url,
            'read' => $this->faker->boolean,
            'user_id' => User::all()->random()->id,
        ];
    }
}
