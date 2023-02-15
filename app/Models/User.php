<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Traits\Uuids;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens, Uuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'avatar',
        'email',
        'bio',
        'password',
        'is_admin',
    ];
    
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'id',
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function friends()
    {
        //with friends table
        return $this->hasMany(Friends::class);
    }

    public function images()
    {
        return $this->hasMany(PostsImages::class);
    }

    public function comments()
    {
        return $this->hasMany(PostsCommentaries::class);
    }

    public function likes()
    {
        return $this->hasMany(PostsLikes::class);
    }

    public function commentariesLikes()
    {
        return $this->hasMany(PostsCommentariesLikes::class);
    }



    public function createNewToken()
    {
        //create new token
        return $this->is_admin ? $this->createToken('admin', ['admin', 'user']) : $this->createToken('user', ['user']);
    }
}
