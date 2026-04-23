<?php

namespace App\Http\Controllers;

use App\Models\ContentArticle;

class ContentArticleController extends Controller
{
    public function index()
    {
        $articles = ContentArticle::published()
            ->orderByDesc('published_at')
            ->get()
            ->groupBy('category');

        return view('articles.index', compact('articles'));
    }

    public function show(string $slug)
    {
        $article  = ContentArticle::published()->where('slug', $slug)->firstOrFail();
        $related  = ContentArticle::published()
            ->where('slug', '!=', $slug)
            ->where(function ($q) use ($article) {
                $q->where('category', $article->category)
                  ->orWhere('area_id', $article->area_id)
                  ->orWhere('job_type_id', $article->job_type_id);
            })
            ->limit(4)
            ->get();

        return view('articles.show', compact('article', 'related'));
    }
}
