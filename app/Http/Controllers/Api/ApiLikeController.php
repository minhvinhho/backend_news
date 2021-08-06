<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LikesRequest;
use App\Models\Like;
use Illuminate\Http\Request;

class ApiLikeController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/likes",
     *   tags={"Likes"},
     *   operationId="get_Likes",
     *   summary="Likes List",
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
    public function getLike() {
        return response()->json(Like::all(), 200);
    }

    /**
     *  @OA\Get(
     *      path="/api/like/{id}",
     *      tags={"Likes"},
     *      summary="get Likes by id",
     *      description="get Likes by id",
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
    public function getLikeById($id) {
        $likes = Like::find($id);
        if(is_null($likes)) {
            return response()->json(['message' => 'Like Not Found'], 404);
        }
        return response()->json($likes::find($id), 200);
    }
/**
        * @OA\Post(
        * path="/api/like",
        * operationId="Likes",
        * tags={"Likes"},
        * summary="Add Likes",
        * description="Add Likes",
        *      security={
        *          {"bearer_token":{}},
        *      },
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"article_id","user_id"},
        *               @OA\Property(property="article_id", type="text"),
        *               @OA\Property(property="user_id", type="text"),
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Add Likes Successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Add Likes Successfully",
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
    public function addLike(LikesRequest $request) {
        $likes = Like::create($request->all());
        return response($likes, 201);
    }
    /**
     *  @OA\Delete(
     *      path="/api/unlike/{id}",
     *      tags={"Likes"},
     *      summary="Unlike Article",
     *      description="Unlike Article",
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
    public function unlike($id) {
        $likes = Like::find($id);
        if(is_null($likes)) {
            return response()->json(['message' => 'Like Not Found'], 404);
        }
        $likes->delete();
        return response()->json(['message' => 'Unlike Successful'], 200);
    }
}
