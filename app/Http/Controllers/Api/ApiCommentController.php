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

     /**
     *  @OA\Get(
     *      path="/api/comment/{id}",
     *      tags={"Comments"},
     *      summary="get Comments by id",
     *      description="get Comments by id",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *      @OA\Response(
     *          response="default",
     *          description="error."
     *      ),
     *  )
     */
    public function getCommentById($id) {
        $comments = Comment::join('users','users.id','=','comments.user_id')
            ->select('comments.id AS comment_id','users.id AS user_id','users.name','comments.content','comments.published_at')
            ->where('article_id',$id)
            ->get();
        return response()->json($comments,200);
    }

    /**
        * @OA\Post(
        * path="/api/comment",
        * operationId="Comments",
        * tags={"Comments"},
        * summary="Add Comments",
        * description="Add Comments",
        *      security={
        *          {"bearer_token":{}},
        *      },
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"content","email","name", "article_id", "user_id"},
        *               @OA\Property(property="content", type="text"),
        *               @OA\Property(property="email", type="email"),
        *               @OA\Property(property="name", type="text"),
        *               @OA\Property(property="article_id", type="text"),
        *               @OA\Property(property="user_id", type="text"),
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Add Comments Successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Add Comments Successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=422,
        *          description="Unprocessable Entity",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(response=400, description="Bad request"),
        *      @OA\Response(response=404, description="Resource Not Found"),
        * )
        **/
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
