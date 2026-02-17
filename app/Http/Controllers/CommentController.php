<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Http\Requests\CommentRequest;
class CommentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(CommentRequest $request)
    {
        $data = $request->validated();

        Comment::create($data);

        return 'comment created successfully';
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        return $comment;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CommentRequest $request, Comment $comment)
    {
        $data = $request->validated();

        Comment::update($data);

        return 'comment updated successfully';
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        return $comment->delete();
    }
}
