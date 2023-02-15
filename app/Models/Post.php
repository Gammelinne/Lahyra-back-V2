<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    use Uuids;

    protected $fillable = [
        'title',
        'body',
        'likes',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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
}
