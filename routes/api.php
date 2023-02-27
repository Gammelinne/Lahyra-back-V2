<?php

use App\Events\Message;
use App\Events\Test;
use App\Http\Controllers\MailController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerifyEmailController;

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
//get all users with function index on UserController only if your are connected
Route::get('user', [UserController::class, 'index'])->middleware(['auth:api', 'scope:admin']);
//delete user with function delete on UserController only if you are admin
Route::delete('user/{id}', [UserController::class, 'delete'])->middleware(['auth:api', 'scope:admin']);
//get user with function show on UserController
Route::get('user/user/getinfos', [UserController::class, 'show'])->middleware(['auth:api', 'scope:user']);
//update user with function update on UserController
Route::put('user/user/updateown', [UserController::class, 'updateOwn'])->middleware(['auth:api', 'scope:user']);
Route::get('user/user/search' , [UserController::class, 'searchUsers'])->middleware(['auth:api', 'scope:user']);
Route::get("user/user/username", [UserController::class, 'getUserByUsername'])->middleware(['auth:api', 'scope:user']);

/* Log routes */
Route::post('register', [UserController::class, 'create']);
Route::post('login', [UserController::class, 'login']);
//logout user with function logout on UserController
Route::post('logout', [UserController::class, 'logout'])->middleware(['auth:api', 'scope:user']);
/* Mail routes */

//send mail with function send on MailController
Route::post('mail', [MailController::class, 'send'])->middleware(['auth:api', 'scope:user']);

// Verify email
Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])->name('verification.verify');

// Resend link to verify email
Route::post('/email/verify/resend', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return response()->json(['message' => 'Verification link sent']);
})->middleware(['auth:api', 'scope:user'])->name('verification.send');

//reset password
Route::post('/forgot-password', [UserController::class, 'forgotPassword'])->name('password.email');
//redirect to frontend after reset password
Route::get('/reset-password/{token}', function (string $token) {
    return redirect(env('FRONTEND_URL') . '/reset-password?token=' . $token);
})->name('password.reset');

Route::post('/reset-password', [UserController::class, 'resetPassword'])->name('password.update');


/* Post routes */

//user function home on UserController
Route::get('post/post/home', [PostController::class, 'home'])->middleware(['auth:api', 'scope:user']);
Route::resource('post', PostController::class)->middleware(['auth:api', 'scope:user']);
Route::post('post/{post}/comment', [PostController::class, 'comment'])->middleware(['auth:api', 'scope:user']);
Route::post('post/{post}/like', [PostController::class, 'like'])->middleware(['auth:api', 'scope:user']);
Route::post('post/{post}/dislike', [PostController::class, 'dislike'])->middleware(['auth:api', 'scope:user']);
Route::post('post/{post}/image', [PostController::class, 'image'])->middleware(['auth:api', 'scope:user']);
Route::get('post/post/search' , [PostController::class, 'searchPosts'])->middleware(['auth:api', 'scope:user']);

/* Commentaries routes */


/* event routes */

//test event
Route::get('/test', function (Request $request) {
    event(new Test($request->data));
    return 'event fired';
});
//test event
Route::get('/message', function (Request $request) {
    event(new Message($request->data));
    return 'event fired';
})->middleware(['auth:api', 'scope:user']);
