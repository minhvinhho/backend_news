<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;

class ApiCategoriesController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/categories",
     *   tags={"Categories"},
     *   operationId="get_categories",
     *   summary="Categories List",
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
    public function getCategory() {
        return response()->json(Category::all(), 200);
    }

    /**
     *  @OA\Get(
     *      path="/api/category/{id}",
     *      tags={"Categories"},
     *      summary="get Categories by id",
     *      description="get Categories by id",
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
    public function getCategoryById($id) {
        $categories = Category::find($id);
        if(is_null($categories)) {
            return response()->json(['message' => 'Category Not Found'], 404);
        }
        return response()->json($categories::find($id), 200);
    }

    public function getArticlesByCategory1(Category $categories)
    {
        $categories->load(['articles' => function ($query) {
            $query->get();
        }]);
        return response()->json(['categories' => $categories]);
    }
}
