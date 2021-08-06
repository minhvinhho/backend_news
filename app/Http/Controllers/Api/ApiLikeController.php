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


    public function getLikeById($id) {
        $likes = Like::find($id);
        if(is_null($likes)) {
            return response()->json(['message' => 'Like Not Found'], 404);
        }
        return response()->json($likes::find($id), 200);
    }

    public function addLike(LikesRequest $request) {
        $likes = Like::create($request->all());
        return response($likes, 201);
    }

    public function disLike($id) {
        $likes = Like::find($id);
        if(is_null($likes)) {
            return response()->json(['message' => 'Like Not Found'], 404);
        }
        $likes->delete();
        return response()->json(['message' => 'Dislike Successful'], 200);
    }
}
