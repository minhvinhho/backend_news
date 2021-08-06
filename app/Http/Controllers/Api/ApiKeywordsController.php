<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Keyword;

class ApiKeywordsController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/keywords",
     *   tags={"Keywords"},
     *   operationId="get_keywords",
     *   summary="Keywords List",
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
    public function getKeyword() {
        return response()->json(Keyword::all(), 200);
    }
    /**
     *  @OA\Get(
     *      path="/api/keyword/{id}",
     *      tags={"Keywords"},
     *      summary="get Keywords by id",
     *      description="get Keywords by id",
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
    public function getKeywordById($id) {
        $keywords = Keyword::find($id);
        if(is_null($keywords)) {
            return response()->json(['message' => 'Keywords Not Found'], 404);
        }
        return response()->json($keywords::find($id), 200);
    }

}
