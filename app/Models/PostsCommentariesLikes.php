<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostsCommentariesLikes extends Model
{
    use HasFactory;
    use Uuids;

    protected $fillable = [
        'user_id',
        'commentary_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commentary()
    {
        return $this->belongsTo(PostsCommentaries::class);
    }
}
