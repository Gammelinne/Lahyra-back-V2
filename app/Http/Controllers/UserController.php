<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;

class UserController extends Controller
{

    //index all users
    public function index()
    {
        return User::all();
    }
    //create user with passport
    public function create(Request $request)
    {
        //validate request data
        //need name, email and password, password_confirmation
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed',
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

    //login user with passport
    public function login(Request $request)
    {
        //validate request data
        //need email and password
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
        //get user if authenticated and return user and token
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
    
    //login user with passport
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

    public function sendVerifyEmail(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        return response()->json([
            'message' => 'Email sent'
        ]);
    }

}
