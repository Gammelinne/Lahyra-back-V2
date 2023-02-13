<?php

namespace App\Http\Controllers;

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
        $post->likes += 1;
        $post->save();

        return response()->json([
            'message' => 'Post liked successfully',
            'post' => $post,
        ], 200);
    }

    public function dislike(Post $post)
    {
        $post->like -= 1;
        $post->save();

        return response()->json([
            'message' => 'Post disliked successfully',
            'post' => $post,
        ], 200);
    }

    public function comment(Request $request, Post $post)
    {
        $request->validate([
            'user_id' => 'required|uuid',
            'comment' => 'required|string',
        ]);

        $post->commentary()->create([
            'user_id' => $request->user_id,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'message' => 'Comment created successfully',
            'post' => $post,
        ], 201);
    }

    public function deleteComment(Post $post, $commentId)
    {
        $post->commentary()->where('id', $commentId)->delete();

        return response()->json([
            'message' => 'Comment deleted successfully',
            'post' => $post,
        ], 200);
    }

    public function likeComment(Post $post, $commentId)
    {
        $post->commentary()->where('id', $commentId)->increment('likes');

        return response()->json([
            'message' => 'Comment liked successfully',
            'post' => $post,
        ], 200);
    }

    public function dislikeComment(Post $post, $commentId)
    {
        $post->commentary()->where('id', $commentId)->decrement('likes');

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
}
