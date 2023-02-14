<?php

namespace Database\Factories;

//import model user 
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */

    public function definition()
    { 
        return [
            'title' => $this->faker->sentence,
            'body' => $this->faker->sentence(10),
            'likes' => $this->faker->numberBetween(0, 100),
            'comments' => $this->faker->numberBetween(0, 100),
            'user_id' => User::all()->random()->id,
        ];
    }
}
