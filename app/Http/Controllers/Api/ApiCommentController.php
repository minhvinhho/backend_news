<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\RepComment;
use Illuminate\Http\Request;

class ApiCommentController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/comments",
     *   tags={"Comments"},
     *   operationId="get_Comments",
     *   summary="Comments List",
     *   @OA\Response(
     *    response=200,
     *    description="success",
     *   ),
     *   @OA\Response(
     *    response=400,
     *    description="error",
     *   ),
     *  )
     */
    public function getComment() {
        return response()->json(Comment::all(), 200);
    }


    public function getCommentById($id) {
        $comments = Comment::join('users','users.id','=','comments.user_id')
            ->select('comments.id AS comment_id','users.id AS user_id','users.name','comments.content','comments.published_at')
            ->where('article_id',$id)
            ->get();
        return response()->json($comments,200);
    }

    public function addComment(CommentRequest $request) {
        $comments = Comment::create($request->all());
        return response($comments, 201);
    }

    public function addRepComment(Request $request){
        $getIDcomment = Comment::where('token',$request->token)->first();
        $addComment = new RepComment();
        $addComment->user_id=$request->user_id;
        $addComment->content=$request->content;
        $addComment->comment_id=$getIDcomment['id'];
        $addComment->save();
        return response()->json(['status'=>true],200);
    }
}
