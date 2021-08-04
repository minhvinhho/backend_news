<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function index()
    {
        if (Auth::user()) {
            $authorsArticleIDs = Article::where('user_id', Auth::user()->id)->pluck('id');
            $likes = Like::whereIn('article_id', $authorsArticleIDs)
                ->with('article', 'user');
        } else {
            $likes = Like::with('article', 'user');
        }
    }
}
