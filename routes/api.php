<?php

use App\Http\Controllers\MailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Auth\Events\Verified;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* User routes */

//register user with function create on UserController
Route::post('user', [UserController::class, 'create']);
//login user with function login on UserController
Route::post('login', [UserController::class, 'login']);
//logout user with function logout on UserController
Route::post('logout', [UserController::class, 'logout']);
//get all users with function index on UserController only for admin with scope admin
Route::get('users', [UserController::class, 'index'])->middleware('scope:admin');

//verify email with function verifyEmail on UserController with a token in the url
Route::get('email/verify/{id}/{hash}', function (Request $request) {
    $user = User::findOrFail($request->id);
    if (!$user->hasVerifiedEmail() && $user->markEmailAsVerified()) {
        event(new Verified($user));
    }
    return redirect()->to(env('FRONTEND_URL'));
})->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
 
    return back()->with('message', 'Verification link sent!');
})->middleware(['scope:user'])->name('verification.send');


/* Mail routes */

//send mail with function send on MailController
Route::post('mail', [MailController::class, 'send']);


