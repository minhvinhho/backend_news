<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticlesRequest;
use App\Models\Article;
use App\Models\Category;

use Illuminate\Http\Request;

class ApiArticlesController extends Controller
{
    public function getArticle() {
        return response()->json(Article::all(), 200);
    }


    public function getArticleById($id) {
        $articles = Article::find($id);
        if(is_null($articles)) {
            return response()->json(['message' => 'Article Not Found'], 404);
        }
        return response()->json($articles::find($id), 200);
    }

    public function addArticle(ArticlesRequest $request) {
        $articles = Article::create($request->all());
        return response($articles, 201);
    }

}
