<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostsRessource;
use Illuminate\Support\Collection;
use App\Models\Friends;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

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
    //create
    public function create(Request $request)
    {
        return 'create';
    }
    //edit
    public function edit(Post $post)
    {
        return 'edit';
    }
    //destroy
    public function destroy(Post $post)
    {
        return 'destroy';
    }
    //image
    public function image(Post $post)
    {
        return 'image';
    }
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
            'images' => 'sometimes|nullable|array|max:5',
        ]);

        $post = Post::create([
            'title' => $request->title,
            'body' => $request->body,
            'user_id' => auth()->user()->id,
        ]);
        if ($request->images) {
            foreach ($request->images as $image) {
                //get size in bytes of image
                $size = (strlen(rtrim($image, '=')) * 3) / 4;
                //it's form 'data:image/ + type + ;base64,'
                $type = explode('/', explode(';', $image)[0])[1];
                $image = str_replace('data:image/' . $type . ';base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageName = $post->id . '_' . uniqid() . '.' . $type;
                //if image is bigger than 2MB, compress it
                if ($size > 2097152) {
                    $image = imagecreatefromstring(base64_decode($image));
                    imagejpeg($image, storage_path('app/public/images/' . $imageName), 30);
                } else {
                    Storage::disk('public')->put('images/' . $imageName, base64_decode($image));
                }
                $post->images()->create([
                    'image' => $imageName,
                    'user_id' => auth()->user()->id,
                ]);
            }
        }

        return response()->json([
            'message' => 'Post created successfully',
            'post' => PostsRessource::make($post),
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

        return response()->json([
            'message' => 'Post updated successfully',
            'post' => PostsRessource::make($post),
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
                'created_at' => Carbon::now()->diffForHumans(),
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
        $user = auth()->user();
        $userPosts = Post::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        $friends = Friends::where('user_id', $user->id)->where('accepted', true)->get();
        //merge friends posts to user posts
        foreach ($friends as $friend) {
            $friendPosts = Post::where('user_id', $friend->friend_id)->orderBy('created_at', 'desc')->get();
            $userPosts = $userPosts->merge($friendPosts);
        }



        //sort posts by date
        $sortedPosts = $userPosts->sortByDesc('created_at');
        $perPage = 8;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentPageItems = $sortedPosts->slice(($currentPage - 1) * $perPage, $perPage);
        $paginatedPosts = new LengthAwarePaginator($currentPageItems, $sortedPosts->count(), $perPage, $currentPage);
        return PostsRessource::collection($paginatedPosts);
    }

    public function searchPosts(Request $request)
    {
        $friends_blocked = Friends::where('user_id', auth()->user()->id)->where('is_blocked', false)->get();

        $post = Post::where('title', 'like', '%' . $request->search . '%')
            ->orWhere('body', 'like', '%' . $request->search . '%')
            ->whereNotIn('user_id', $friends_blocked->pluck('friend_id'))
            ->where('is_private', false)->paginate(5);


        return PostsRessource::collection($post);
    }
}
