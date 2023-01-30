<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /* get all users */
    public function index()
    {
        return User::all();
    }

    /* create user */
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed', //password_confirmation is required
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'is_admin' => $request->admin_password == env('ADMIN_KEY') ? true : false,
        ]);

        event(new Registered($user));

        //redirect to login
        return response()->json([
            'message' => 'User created successfully'
        ], 201);
    }

    /* login user */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = request(['email', 'password']);
        if (!auth()->attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
        $user = $request->user();
        $token = $user->createNewToken();
        return response()->json([
            'user' => $user,
            'access_token' => $token->accessToken,
            'expires_at' => Carbon::parse(
                $token->token->expires_at
            )->toDateTimeString()
        ]);
    }
    
    /* logout user */
    public function logout(Request $request)
    {
        try {
            $request->user()->token()->revoke();
            return response()->json([
                'message' => 'Successfully logged out'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong'
            ]);
        }
    }

    /* verify email */

    public function sendVerifyEmail(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        return response()->json([
            'message' => 'Email sent'
        ]);
    }

    /* delete user */

    public function delete(User $user)
    {
        $user->delete();
        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }

    /* send password reset link */

    public function forgotPassword(Request $request){
        $request->validate([
            'email' => 'required|email',
        ]);
     
        $status = Password::sendResetLink(
            $request->only('email')
        );
     
        return $status === Password::RESET_LINK_SENT
                    ? response()->json(['message' => __($status)])
                    : response()->json(['message' => __($status)]);
    }

    /* reset password */
    public function resetPassword(Request $request){
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);
     
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
     
                $user->save();
     
                event(new PasswordReset($user));
            }
        );
     
        return $status === Password::PASSWORD_RESET
                    ? response()->json(['message' => __($status)])
                    : response()->json(['message' => __($status)]);
    }

}
