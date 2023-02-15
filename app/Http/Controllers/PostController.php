<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostsRessource;
use App\Models\Friends;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        return Post::all();
    }

    public function show(Post $post)
    {
        return $post;
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
            'user_id' => 'required|uuid',
            'images' => 'sometimes|nullable|array',
        ]);

        $post = Post::create($request->all());

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $post->images()->create([
                    'image' => $image->store('images', 'public'),
                    'user_id' => $request->user_id,
                ]);
            }
        }

        return response()->json([
            'message' => 'Post created successfully',
            'post' => $post,
        ], 201);
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
            'user_id' => 'required|uuid',
            'images' => 'sometimes|nullable|array',
        ]);

        $post->update($request->all());

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $post->images()->create([
                    'image' => $image->store('images', 'public'),
                    'user_id' => $request->user_id,
                ]);
            }
        }

        return response()->json([
            'message' => 'Post updated successfully',
            'post' => $post,
        ], 200);
    }

    public function delete(Post $post)
    {
        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully',
        ], 200);
    }

    public function like(Post $post)
    {
        $post->likes()->create([
            'user_id' => auth()->user()->id,
        ]);
        $post->save();

        //create message event
        event(new \App\Events\Message([
            'like' => [
                'user' => auth()->user(),
                'post_id' => $post->id,
            ],
        ]));

        return response()->json([
            'message' => 'Post liked successfully',
            'post' => $post,
        ], 200);
    }

    public function dislike(Post $post)
    {
        $post->likes()->where('user_id', auth()->user()->id)->delete();
        $post->save();

        event(new \App\Events\Message([
            'dislike' => [
                'user' => auth()->user(),
                'post_id' => $post->id,
            ],
        ]));

        return response()->json([
            'message' => 'Post disliked successfully',
            'post' => $post,
        ], 200);
    }

    public function comment(Request $request, Post $post)
    {
        $user_id = auth()->user()->id;
        $post->comments()->create([
            'commentary' => $request->commentary,
            'user_id' => $user_id,
        ]);
        //notifify
        event(new \App\Events\Message([
            'comment' => [
                'commentary' => $request->commentary,
                'user' => auth()->user(),
                'post_id' => $post->id,
            ],
        ]));
        return response()->json([
            'message' => 'Comment created successfully',
            'post' => $post,
        ], 201);
    }

    public function deleteComment(Request $request, Post $post)
    {
        $post->commentary()->where('id', $request->commentary)->delete();

        return response()->json([
            'message' => 'Comment deleted successfully',
            'post' => $post,
        ], 200);
    }

    public function likeComment(Post $post, $commentId)
    {
        $post->commentary()->likes()->create([
            'user_id' => auth()->user()->id,
        ]);
        return response()->json([
            'message' => 'Comment liked successfully',
            'post' => $post,
        ], 200);
    }

    public function dislikeComment(Post $post, $commentId)
    {
        $post->commentary()->where('id', $commentId)->first()->likes()->where('user_id', auth()->user()->id)->delete();
        return response()->json([
            'message' => 'Comment disliked successfully',
            'post' => $post,
        ], 200);
    }

    public function replyComment(Request $request, Post $post, $commentId)
    {
        $request->validate([
            'user_id' => 'required|uuid',
            'comment' => 'required|string',
        ]);

        $post->commentary()->where('id', $commentId)->first()->replies()->create([
            'user_id' => $request->user_id,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'message' => 'Reply created successfully',
            'post' => $post,
        ], 201);
    }

    public function deleteReply(Post $post, $commentId, $replyId)
    {
        $post->commentary()->where('id', $commentId)->first()->replies()->where('id', $replyId)->delete();

        return response()->json([
            'message' => 'Reply deleted successfully',
            'post' => $post,
        ], 200);
    }

    public function likeReply(Post $post, $commentId, $replyId)
    {
        $post->commentary()->where('id', $commentId)->first()->replies()->where('id', $replyId)->increment('likes');

        return response()->json([
            'message' => 'Reply liked successfully',
            'post' => $post,
        ], 200);
    }

    public function dislikeReply(Post $post, $commentId, $replyId)
    {
        $post->commentary()->where('id', $commentId)->first()->replies()->where('id', $replyId)->decrement('likes');

        return response()->json([
            'message' => 'Reply disliked successfully',
            'post' => $post,
        ], 200);
    }

    public function home(Request $request)
    {
        $user = $request->user();
        $friends = Friends::where('user_id', $user->id)->get();
        $friends->push($user);
        //return post of array of friends sort by date and paginate 10
        return PostsRessource::collection(Post::whereIn('user_id', $friends->pluck('friend_id'))->orderBy('created_at', 'desc')->paginate(8));
    }
}
