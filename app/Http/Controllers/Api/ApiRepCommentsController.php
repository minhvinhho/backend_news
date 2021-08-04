<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RepCommentRequest;
use App\Models\RepComment;

class ApiRepCommentsController extends Controller
{
    public function getRepComment() {
        return response()->json(RepComment::all(), 200);
    }


    public function getRepCommentById($id) {
        $repcomments = RepComment::find($id);
        if(is_null($repcomments)) {
            return response()->json(['message' => 'Rep Comment Not Found'], 404);
        }
        return response()->json($repcomments::find($id), 200);
    }

    public function addRepComment(RepCommentRequest $request) {
        $repcomments = RepComment::create($request->all());
        return response($repcomments, 201);
    }

    // get rep cmt theo id cmt
    // get rep cmt theo id user

}
