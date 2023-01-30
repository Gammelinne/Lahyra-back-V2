<?php

use App\Http\Controllers\MailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerifyEmailController;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
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
//delete user with function delete on UserController only if you are admin
Route::delete('user/{id}', [UserController::class, 'delete'])->middleware(['auth:api', 'scope:admin']);

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

//reset password


Route::post('/forgot-password', [UserController::class, 'forgotPassword'])->name('password.email');

Route::get('/reset-password/{token}', function (string $token) {
    return redirect(env('FRONTEND_URL') . '/reset-password?token=' . $token);
})->name('password.reset');

Route::post('/reset-password', [UserController::class, 'resetPassword'])->name('password.update');