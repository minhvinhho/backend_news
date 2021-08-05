<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ApiCategoriesController;
use App\Http\Controllers\Api\ApiKeywordsController;
use App\Http\Controllers\Api\ApiArticlesController;
use App\Http\Controllers\Api\ApiCommentController;
use App\Http\Controllers\Api\ApiLikeController;
use App\Http\Controllers\Api\ApiRepCommentsController;


Route::group(['prefix'=>'user'],function(){
    Route::post('register',[AuthController::class,'Registration']);
    Route::get('verifi/{token}',[AuthController::class,'userVerification']);
    Route::post('login',[AuthController::class,'Login']);
    Route::get('me',[AuthController::class,'checkInfo'])->middleware('auth:api');
    Route::get('resend',[AuthController::class,'resendEmailVerification'])->middleware('auth:api');
    Route::post('forgetPassword',[AuthController::class,'sendEmailForgetPassword']);
    Route::post('handleForgetPassword',[AuthController::class,'handleForgetPassword']);
    Route::post('google',[AuthController::class,'handleLoginGoogle']);
    Route::get('/avatar/{name}',[AuthController::class,'getAvatar']);
    Route::post('/avatar',[AuthController::class,'updateAvatar']);
    Route::get('/RandomlySelectAuthor',[AuthController::class,'RandomlySelectAuthor']);
});

Route::group(['prefix'=>'articles'],function(){
    Route::get('confirmed/{id}/{amount}',[ApiArticlesController::class,'GetPostConfirmed']);
    Route::get('unconfirmed/{id}/{amount}',[ApiArticlesController::class,'GetPostUnconfirmed'])->middleware('auth:api');
    Route::post('searchPost',[ApiArticlesController::class,'searchPost']);
    Route::get('favoritePost',[ApiArticlesController::class,'favoritePost']);
    Route::get('searchPost/{key}',[ApiArticlesController::class,'searchPost']);
    Route::get('GetFavoriteThemedArticles/{theme}',[ApiArticlesController::class,'GetFavoriteThemedArticles']);
    Route::get('GetTheLatestArticles',[ApiArticlesController::class,'GetTheLatestArticles']);
    Route::get('GetTheLatestThemedArticles/{theme}',[ApiArticlesController::class,'GetTheLatestThemedArticles']);
    Route::get('GetRelatedArticles/{id}',[ApiArticlesController::class,'GetRelatedArticles']);
});



Route::group(['prefix'=>'article'],function(){
    Route::get('{id}',[ApiArticlesController::class,'getArticleById']);
    Route::post('likeArticle/{id}',[ApiArticlesController::class,'LikePost']);
    Route::post('increaseViews',[ApiArticlesController::class,'increaseViews']);
});

Route::Post("/register",[AuthController::class,'register']);
Route::Post("/login",[AuthController::class,'signin']);

Route::middleware('auth:api')->group(function () {
    Route::Get('/me',[AuthController::class,'getMe']);
    Route::Post('/change_password',[AuthController::class,'changePassword']);
});

// Get all category
Route::get('/categories', [ApiCategoriesController::class, 'getCategory']);

// Get category detail
Route::get('/category/{id}', [ApiCategoriesController::class, 'getCategoryById']);

// Get all keyword
Route::get('/keywords', [ApiKeywordsController::class, 'getKeyword']);

// Get keyword detail
Route::get('/keyword/{id}', [ApiKeywordsController::class, 'getKeywordById']);

// Get all article
Route::get('/articles', [ApiArticlesController::class, 'getArticle']);

// Get article detail
Route::get('/article/{id}', [ApiArticlesController::class, 'getArticleById']);

// Add article
Route::post('/article', [ApiArticlesController::class, 'addArticle'])->middleware('auth:api');

// Get all Comment
Route::get('/comments', [ApiCommentController::class, 'getComment']);

// Get comment detail
Route::get('/comment/{id}', [ApiCommentController::class, 'getCommentById']);

// Add comment
Route::post('/comment', [ApiCommentController::class, 'addComment'])->middleware('auth:api');

// Get all Like
Route::get('/likes', [ApiLikeController::class, 'getLike']);

// Get Like detail
Route::get('/like/{id}', [ApiLikeController::class, 'getLikeById']);

// Add Like
Route::post('/like', [ApiLikeController::class, 'addLike'])->middleware('auth:api');

// DisLike
Route::post('/dislike/{id}', [ApiLikeController::class, 'disLike'])->middleware('auth:api');

// Get all rep cmt
Route::get('/repcomments', [ApiRepCommentsController::class, 'getRepComment']);

// Get rep cmt detail
Route::get('/repcomment/{id}', [ApiRepCommentsController::class, 'getRepCommentById']);

// Add rep cmt
Route::post('/repcomment', [ApiRepCommentsController::class, 'addRepComment'])->middleware('auth:api');