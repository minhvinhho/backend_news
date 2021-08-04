<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;

class ApiCommentController extends Controller
{
    public function getComment() {
        return response()->json(Comment::all(), 200);
    }


    public function getCommentById($id) {
        $comments = Comment::find($id);
        if(is_null($comments)) {
            return response()->json(['message' => 'Comment Not Found'], 404);
        }
        return response()->json($comments::find($id), 200);
    }

    public function addComment(CommentRequest $request) {
        $comments = Comment::create($request->all());
        return response($comments, 201);
    }

    // get cmt theo article
}
