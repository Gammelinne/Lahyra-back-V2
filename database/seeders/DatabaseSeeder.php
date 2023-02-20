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
        User::factory(20)->create();
        Post::factory(30)->create();
        Post::factory(10)->hasImages(3)->create();
        Post::factory(10)->hasComments(3)->create();
        Post::factory(10)->hasImages(3)->hasComments(3)->create();
        Friends::factory(100)->create();
        Notification::factory(100)->create();

        $Kylian = User::create([
            'name' => 'Kylian',
            'username' => 'Gameline',
            'bio' => 'Creator of this project',
            'email' => 'kylian.noe14@gmail.com',
            'email_verified_at' => now(),
            'is_admin' => true,
            'avatar' => 'https://www.w3schools.com/howto/img_avatar.png',
            'password' => '$2y$10$H9YRXS0Ic7Eg8HR6MEDU4eXXu.D.R.faFxem9eXoG2rStvZcRytK.', //Kylian
        ]);

        $Ambre = User::create([
            'name' => 'Moussy Ambre',
            'username' => 'Moussemousse',
            'bio' => '',
            'email' => 'ambre@evantis.fr',
            'email_verified_at' => now(),
            'is_admin' => false,
            'avatar' => 'https://www.w3schools.com/howto/img_avatar.png',
            'password' => '$2y$10$N7rDFxDYaaapaBWyEe8W.O0n9.OJWr.faraqwJiEH7C7vGBNkc.1G', //Ambre
        ]);

        $Thomas = User::create([
            'name' => 'Thomas',
            'username' => 'Thomas rnlt',
            'bio' => '',
            'email' => 'renaultthomas0@gmail.com',
            'email_verified_at' => now(),
            'is_admin' => false,
            'avatar' => 'https://www.w3schools.com/howto/img_avatar.png',
            'password' => '$2y$10$S7TQ7zM/ccyB9Tlb94eqUuRjYdsiNE6A4B5tp36fjQCamhFP3FL7m', //Thomas
        ]);
        $Sandrine = User::create([
            'name' => 'Sandrine',
            'username' => 'sandrine',
            'bio' => '',
            'email' => 'sandrine3014@aol.com',
            'email_verified_at' => now(),
            'is_admin' => false,
            'avatar' => 'https://www.w3schools.com/howto/img_avatar.png',
            'password' => '$2y$10$D5GLu1PjgP9sz.M4LWxNkekUli/8SjIW2XpQWWUDDlqMm2n6kxil6', //Sandrine
        ]);

        // Kylian, Ambre, Thomas, Sandrine are friends

        Friends::create([
            'user_id' => $Kylian->id,
            'friend_id' => $Ambre->id,
            'accepted' => true,
        ]);

        Friends::create([
            'user_id' => $Kylian->id,
            'friend_id' => $Thomas->id,
            'accepted' => true,

        ]);

        Friends::create([
            'user_id' => $Kylian->id,
            'friend_id' => $Sandrine->id,
            'accepted' => true,

        ]);

        Friends::create([
            'user_id' => $Ambre->id,
            'friend_id' => $Kylian->id,
            'accepted' => true,
        ]);

        Friends::create([
            'user_id' => $Ambre->id,
            'friend_id' => $Thomas->id,
            'accepted' => true,
        ]);

        Friends::create([
            'user_id' => $Ambre->id,
            'friend_id' => $Sandrine->id,
            'accepted' => true,
        ]);

        Friends::create([
            'user_id' => $Thomas->id,
            'friend_id' => $Kylian->id,
            'accepted' => true,
        ]);

        Friends::create([
            'user_id' => $Thomas->id,
            'friend_id' => $Ambre->id,
            'accepted' => true,
        ]);

        Friends::create([
            'user_id' => $Thomas->id,
            'friend_id' => $Sandrine->id,
            'accepted' => true,
        ]);

        Friends::create([
            'user_id' => $Sandrine->id,
            'friend_id' => $Kylian->id,
            'accepted' => true,
        ]);

        Friends::create([
            'user_id' => $Sandrine->id,
            'friend_id' => $Ambre->id,
            'accepted' => true,
        ]);

        Friends::create([
            'user_id' => $Sandrine->id,
            'friend_id' => $Thomas->id,
            'accepted' => true,
        ]);
    }
}
