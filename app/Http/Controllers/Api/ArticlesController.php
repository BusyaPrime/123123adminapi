<?php

namespace App\Http\Controllers\Api;

use App\Domain\Articles\Models\Article;
use App\Domain\Articles\Resources\ArticleResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ArticlesController extends Controller
{
    public function index(){
        $articles = Article::withTranslation()->orderBy('id', 'desc')->get();
        return ArticleResource::collection($articles);
    }

    public function show(Article $article)
    {
        $_article = Article::withTranslation()->find($article->id);
        return ArticleResource::make($_article);
    }
}
