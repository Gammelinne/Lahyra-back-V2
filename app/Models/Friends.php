<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friends extends Model
{
    use HasFactory;
    use Uuids;

    protected $fillable = [
        'user_id',
        'friend_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function friend()
    {
        return $this->belongsTo(User::class);
    }



    

    public function scopeFriends($query, $user_id)
    {
        return $query->where('user_id', $user_id)->where('status', 1);
    }

    public function scopeFriendRequests($query, $user_id)
    {
        return $query->where('friend_id', $user_id)->where('status', 0);
    }

    public function scopeFriendRequestsSent($query, $user_id)
    {
        return $query->where('user_id', $user_id)->where('status', 0);
    }

    public function scopeIsFriend($query, $user_id, $friend_id)
    {
        return $query->where('user_id', $user_id)->where('friend_id', $friend_id)->where('status', 1);
    }

    public function scopeIsFriendRequest($query, $user_id, $friend_id)
    {
        return $query->where('user_id', $user_id)->where('friend_id', $friend_id)->where('status', 0);
    }
}
