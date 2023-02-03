<?php

use Illuminate\Console\Scheduling\Event;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('testchannel', function ($user) {
    return $user;
}); 

Broadcast::channel('message', function ($user) {
    return $user;
});

// Broadcast::channel('message.{id}', function ($user, $id) {
//     return $user->id === $id;
// });