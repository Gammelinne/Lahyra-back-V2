<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PostImageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|array',
        ]);

        $image = $request->file('image');

        $image->store('images', 'public');


        return response()->json([
            'message' => 'Image uploaded successfully',
        ], 201);
    }
}
