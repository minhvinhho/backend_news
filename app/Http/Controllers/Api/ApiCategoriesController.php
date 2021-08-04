<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;

class ApiCategoriesController extends Controller
{
    public function getCategory() {
        return response()->json(Category::all(), 200);
    }

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
