<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Keyword;

class ApiKeywordsController extends Controller
{
    public function getKeyword() {
        return response()->json(Keyword::all(), 200);
    }

    public function getKeywordById($id) {
        $keywords = Keyword::find($id);
        if(is_null($keywords)) {
            return response()->json(['message' => 'Keywords Not Found'], 404);
        }
        return response()->json($keywords::find($id), 200);
    }

    //get keyword theo article
}
