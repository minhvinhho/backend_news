<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticlesRequest;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;


class ApiArticlesController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/articles",
     *   tags={"Articles"},
     *   operationId="get_articles",
     *   summary="Articles List",
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
    public function getArticle() {
        return response()->json(Article::all(), 200);
    }

    /**
     *  @OA\Get(
     *      path="/api/article/{id}",
     *      tags={"Articles"},
     *      summary="get article by id",
     *      description="get article by id",
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
    public function getArticleById($id) {
        $articles = Article::join('categories','articles.category_id','=','categories.id')
            ->join('users','articles.user_id','=','users.id')
            ->select('users.name','users.avatar_link','categories.name as name_categorie','articles.background_img',
                'articles.heading','articles.podcast','articles.content','articles.hit_count','articles.comment_count',
                'articles.like_count','articles.published_at')
            ->where('articles.id','=',$id)
            ->get();
        return response()->json($articles);
    }
     /**
        * @OA\Post(
        * path="/api/article",
        * operationId="Article",
        * tags={"Articles"},
        * summary="Add Articles",
        * description="Add Articles",
        *      security={
        *          {"bearer_token":{}},
        *      },
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"heading","background_img","podcast", "content", "user_id","category_id"},
        *               @OA\Property(property="heading", type="text"),
        *               @OA\Property(property="content", type="text"),
        *               @OA\Property(property="user_id", type="text"),
        *               @OA\Property(property="category_id", type="text"),
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Add Articles Successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Add Articles Successfully",
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

    public function addArticle(ArticlesRequest $request) {
        $data = $request->validated();
        $articles = Article::create($data);
        return response()->json(['status'=>true,'data'=>$articles],200);
    }

    public function GetPostConfirmed($id,$amount){
        $data = Article::orderBy('hit_count','DESC')
            ->join('categories','articles.category_id','=','categories.id')
            ->join('users','users.id','=','articles.user_id')
            ->select('articles.id','articles.background_img','articles.heading','categories.name as name_categorie','articles.hit_count','articles.like_count')
            ->where('is_published','=','1')
            ->where('users.name','=',$id)
            ->take($amount)
            ->get();
        return response()->json(['data'=>$data],200);
    }
    public function GetPostUnconfirmed($id,$amount){
        $data = Article::join('categories','articles.category_id','=','categories.id')
            ->join('users','users.id','=','articles.user_id')
            ->select('articles.id','articles.background_img','articles.heading','categories.name as name_categorie')
            ->where('is_published','=','0')
            ->where('user_id','=',$id)
            ->take($amount)
            ->get();
        return response()->json(['data'=>$data],200);
    }
    public function LikePost(Request $request,$id){
        $data = Article::where('id',$id)->first();
        $data['like_count']=$request->like_count;
        $data->save();
        return response()->json(['like_count'=>$data['like_count']],200);
    }

    public function searchPost(Request $request){
        $data = Article::join('categories','articles.category_id','=','categories.id')
            ->join('users','articles.user_id','=','users.id')
            ->select('users.name','users.avatar_link','categories.name as name_categorie','articles.id','articles.background_img','articles.background_img',
                'articles.heading','articles.podcast','articles.content','articles.hit_count','articles.comment_count',
                'articles.like_count','articles.published_at')
            ->where('is_published','=','1')
            ->where('articles.heading','like', '%'. $request->key . '%')
            ->get();
        return response()->json($data);
    }
    public function increaseViews(Request $request){
        $data = Article::where('id',$request->id)->update(['hit_count'=>$request->hit_count]);
        return response()->json(['status'=>true],200);
    }
    public function favoritePost(){
        $articles = Article::orderBy('hit_count','DESC')
            ->join('categories','articles.category_id','=','categories.id')
            ->join('users','articles.user_id','=','users.id')
            ->select('users.name','users.avatar_link','categories.name as name_categorie','articles.id','articles.background_img',
                'articles.heading','articles.podcast','articles.hit_count','articles.published_at')
            ->take(10)
            ->get();
        return response()->json($articles,200);
    }
    public function GetFavoriteThemedArticles($theme){
        $articles = Article::orderBy('hit_count','DESC')
            ->join('categories','articles.category_id','=','categories.id')
            ->join('users','articles.user_id','=','users.id')
            ->select('users.name','users.avatar_link','categories.name as name_categorie','articles.id','articles.background_img',
                'articles.heading','articles.podcast','articles.hit_count','articles.published_at')
            ->where('articles.category_id',$theme)
            ->take(10)
            ->get();
        return response()->json($articles,200);
    }
    public function GetTheLatestArticles(){
        $articles = Article::orderBy('published_at','DESC')
            ->join('categories','articles.category_id','=','categories.id')
            ->join('users','articles.user_id','=','users.id')
            ->select('users.name','users.avatar_link','categories.name as name_categorie','articles.id','articles.background_img',
                'articles.heading','articles.podcast','articles.content','articles.hit_count','articles.comment_count',
                'articles.like_count','articles.published_at')
            ->get();
        return response()->json($articles,200);
    }
    public function GetTheLatestThemedArticles($theme){
        $articles = Article::orderBy('published_at','DESC')
            ->join('categories','articles.category_id','=','categories.id')
            ->join('users','articles.user_id','=','users.id')
            ->select('users.name','users.avatar_link','categories.name as name_categorie','articles.id','articles.background_img',
                'articles.heading','articles.podcast','articles.content','articles.hit_count','articles.comment_count',
                'articles.like_count','articles.published_at')
            ->where('articles.category_id',$theme)
            ->get();
        return response()->json($articles,200);
    }
    public function GetRelatedArticles($id){
        $category_id = Article::where('id',$id)->first();
        $articles = Article::join('categories','articles.category_id','=','categories.id')
            ->join('users','articles.user_id','=','users.id')
            ->select('users.name','users.avatar_link','articles.background_img','articles.id','articles.heading',
                'articles.like_count','articles.published_at')
            ->where('articles.category_id','=',$category_id['category_id'])
            ->inRandomOrder()
            ->take(4)
            ->get();
        return response()->json($articles);
    }

}
