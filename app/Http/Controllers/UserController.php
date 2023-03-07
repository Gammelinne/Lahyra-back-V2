<?php

namespace App\Http\Controllers;

use App\Events\Test;
use App\Http\Resources\PostsRessource;
use App\Http\Resources\UserInfoRessource;
use App\Http\Resources\UserResource;
use App\Models\Friends;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /* get all users */
    public function index()
    {
        return User::all();
    }

    /* get user */
    public function show()
    {
        //create pagination
        return UserInfoRessource::make(auth()->user());
    }

    /* update user */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email,' . $user->id, //email must be unique except current user
            'password' => 'sometimes|nullable|string|confirmed', //password_confirmation is required
        ]);
        $user->update($request->all());

        return response()->json([
            'message' => 'User updated successfully'
        ], 200);
    }

    /* create user */
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'username' => 'required|string|unique:users',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed', //password_confirmation is required
        ]);
        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'is_admin' => $request->admin_password == env('ADMIN_KEY') ? true : false,
        ]);

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
            'user' => [
                'user' => UserResource::make($user),
                'access_token' => $token->accessToken,
                'expires_at' => Carbon::parse(
                    $token->token->expires_at
                )->toDateTimeString()
            ]

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

    public function forgotPassword(Request $request)
    {
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
    public function resetPassword(Request $request)
    {
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

    public function updateOwn(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email,' . auth()->user()->id,
            'username' => 'required|string|unique:users,username,' . auth()->user()->id,
            'bio' => 'string',


        ]);
        User::where('id', auth()->user()->id)->update($request->all());

        return response()->json([
            'message' => 'User updated successfully'
        ], 200);
    }

    public function searchUsers(Request $request)
    {
        //return all users and posts where user.friends.is_blocked = false and post.is_private = false
        $friends_blocked = Friends::where('user_id', auth()->user()->id)->where('is_blocked', true)->get();

        $users = UserResource::collection(User::where('name', 'like', '%' . $request->search . '%')
            ->orWhere('username', 'like', '%' . $request->search . '%')
            ->orWhere('email', 'like', '%' . $request->search . '%')
            ->whereNotIn('id', $friends_blocked->pluck('friend_id'))->paginate(5));
        return $users;
    }

    public function getUserByUsername(Request $request)
    {
        Log::info($request->username);
        $user = User::where('username', $request->username)->first();
        Log::info($user);
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        return UserInfoRessource::make($user);
    }

    public function deletePost(Post $post)
    {
        //check if user is owner of post
        if ($post->user_id != auth()->user()->id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }else{
            $post->delete();
            return response()->json([
                'message' => 'Post deleted successfully'
            ], 200);
        }
    }

    public function inviteFriend(Request $request){
        //request->username
        $user = User::where('username', $request->username)->first();
        if(!$user){
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        $friend = Friends::where('user_id', auth()->user()->id)->where('friend_id', $user->id)->first();
        if($friend){
            return response()->json([
                'message' => 'User is already your friend'
            ], 400);
        } else {
            Friends::create([
                'user_id' => $user->id,
                'friend_id' => auth()->user()->id,
                'is_blocked' => false,
                'is_accepted' => false,
            ]);
            return response()->json([
                'message' => 'Friend request sent'
            ], 200);
        }
    }

    public function acceptFriend(Request $request){
        //request->username
        $user = User::where('username', $request->username)->first();
        if(!$user){
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        $friend = Friends::where('user_id', auth()->user()->id)->where('friend_id', $user->id)->first();
        if($friend){
            $friend->is_accepted = true;
            $friend->save();
            return response()->json([
                'message' => 'Friend request accepted'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Friend request not found'
            ], 404);
        }
    }
}
