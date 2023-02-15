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
        $user_id = User::all()->random()->id;
        //friend_id is a user_id that is not the same as user_id
        $friend_id = User::where('id', '!=', $user_id)->get()->random()->id;
        return [
            'user_id' => $user_id,
            'friend_id' => $friend_id,
            'accepted' => $this->faker->boolean,
            'is_blocked' => $this->faker->boolean,
        ];
    }
}
