<?php

namespace App\Http\Controllers;

use App\Models\ContentArticle;
use App\Models\Job;
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

        $jobs = Job::active()
            ->whereNotNull('email_verified_at')
            ->where('is_admin_hidden', false)
            ->get(['token', 'updated_at', 'source']);

        return response()
            ->view('sitemap', compact('articles', 'areas', 'jobs'))
            ->header('Content-Type', 'application/xml');
    }
}
