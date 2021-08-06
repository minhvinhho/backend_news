<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RepCommentRequest;
use App\Models\RepComment;

class ApiRepCommentsController extends Controller
{
        /**
     * @OA\Get(
     *   path="/api/repcomments",
     *   tags={"RepComments"},
     *   operationId="get_repcomments",
     *   summary="Rep Comments List",
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
    public function getRepComment() {
        return response()->json(RepComment::all(), 200);
    }

/**
     *  @OA\Get(
     *      path="/api/repcomment/{id}",
     *      tags={"RepComments"},
     *      summary="Rep Comments by id",
     *      description="Rep Comments by id",
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
    public function getRepCommentById($id) {
        $repcomments = RepComment::find($id);
        if(is_null($repcomments)) {
            return response()->json(['message' => 'Rep Comment Not Found'], 404);
        }
        return response()->json($repcomments::find($id), 200);
    }
/**
        * @OA\Post(
        * path="/api/repcomment",
        * operationId="repcomment",
        * tags={"RepComments"},
        * summary="Add Rep Comments",
        * description="Add Rep Comments",
        *      security={
        *          {"bearer_token":{}},
        *      },
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"comment_id","user_id","content"},
        *               @OA\Property(property="comment_id", type="text"),
        *               @OA\Property(property="user_id", type="text"),
        *               @OA\Property(property="content", type="text"),
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Add Rep Comments Successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Add Rep Comments Successfully",
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
    public function addRepComment(RepCommentRequest $request) {
        $repcomments = RepComment::create($request->all());
        return response($repcomments, 201);
    }
}
