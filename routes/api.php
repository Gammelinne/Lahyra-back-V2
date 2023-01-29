<?php

use App\Http\Controllers\MailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerifyEmailController;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Laravel\Passport\Http\Middleware\CheckClientCredentials;
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
//get all users with function index on UserController only if your are connected
Route::get('user', [UserController::class, 'index'])->middleware(['auth:api', 'scope:admin']); 
/*
//sen email with function sendVerifyEmail on UserController
Route::post('user/verify', [UserController::class, 'sendVerifyEmail'])->middleware(['auth:api', 'scope:user']);
//verify email with function verify on UserController
Route::get('user/verify/{id}/{hash}', [UserController::class, 'verifyEmail']);*/

/* Mail routes */

//send mail with function send on MailController
Route::post('mail', [MailController::class, 'send'])->middleware(['auth:api', 'scope:user']);


// Verify email
Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->name('verification.verify');

// Resend link to verify email
Route::post('/email/verify/resend', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return response()->json([
        'message' => 'Verification link sent'
    ]);
})   ->middleware(['auth:api', 'scope:user'])
    ->name('verification.send');