<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Http\Requests\PostRequest;
class PostController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {
        $data = $request->validated();

        Post::create($data);

        return 'post created successfully';
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return $post;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, Post $post)
    {
        $data = $request->validated();

        $post->update($data);

        return 'post updated successfully';
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        return $post->delete();
    }
}
