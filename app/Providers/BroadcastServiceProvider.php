<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /* !!! Never change this line, It's to allow private channel !!! */
        Broadcast::routes(['middleware' => ['auth:api', 'scope:user']]);

        require base_path('routes/channels.php');
    }
}
