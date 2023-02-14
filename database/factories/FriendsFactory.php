<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Friends>
 */
class FriendsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {   
        return [
            'user_id' => User::all()->random()->id,
            'friend_id' => User::all()->random()->id,
            'accepted' => $this->faker->boolean,
            'is_blocked' => $this->faker->boolean,
        ];
    }
}
