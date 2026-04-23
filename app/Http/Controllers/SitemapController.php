<?php

namespace App\Http\Controllers;

use App\Models\ContentArticle;
use App\Models\MasterArea;

class SitemapController extends Controller
{
    public function index()
    {
        $articles = ContentArticle::published()
            ->orderByDesc('updated_at')
            ->get(['slug', 'updated_at']);

        $areas = MasterArea::active()
            ->where('prefecture', '沖縄県')
            ->get(['slug']);

        return response()
            ->view('sitemap', compact('articles', 'areas'))
            ->header('Content-Type', 'application/xml');
    }
}
