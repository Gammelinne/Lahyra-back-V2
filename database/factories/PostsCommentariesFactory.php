<?php

namespace Database\Factories;


use App\Models\User;
use App\Models\Post;
use App\Models\PostsCommentaries;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PostsCommentariesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'commentary' => $this->faker->sentence(10),
            'likes' => $this->faker->numberBetween(0, 100),
            'user_id' => User::all()->random()->id,
            'post_id' => Post::all()->random()->id,
            'commentary_id' => rand(0, 10) == 10 ? PostsCommentaries::all()->random()->id : null,
        ];
    }
}
