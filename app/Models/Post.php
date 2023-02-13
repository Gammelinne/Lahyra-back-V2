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
        'dislikes',
        'comments',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(PostImage::class);
    }

    public function commentary()
    {
        return $this->hasMany(PostCommentary::class);
    }
}
