<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\Friends;
use App\Models\Notification;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(50)->create();
        Post::factory(30)->create();
        Post::factory(10)->hasImages(3)->create();
        Post::factory(10)->hasComments(3)->create();
        Post::factory(10)->hasImages(3)->hasComments(3)->create();
        Friends::factory(10)->create();
        Notification::factory(100)->create();

        
    }
}
