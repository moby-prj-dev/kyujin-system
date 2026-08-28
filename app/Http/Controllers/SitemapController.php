<?php

namespace App\Http\Controllers;

use App\Models\ContentArticle;
use App\Models\Job;
use App\Models\MasterArea;
use Illuminate\Support\Facades\DB;

class SitemapController extends Controller
{
    public function index()
    {
        $articles = ContentArticle::published()
            ->orderByDesc('updated_at')
            ->get(['slug', 'updated_at']);

        $areas = MasterArea::active()
            ->where('prefecture', '沖縄県')
            ->get(['id', 'slug']);

        $jobs = Job::active()
            ->whereNotNull('email_verified_at')
            ->where('is_admin_hidden', false)
            ->get(['token', 'updated_at', 'source']);

        // 求人が実在する area × jobType の組み合わせのみ sitemap に含める
        // (存在しない組み合わせをGoogleに送るとthin content扱いになる)
        $areaJobTypeCombos = DB::table('job_areas')
            ->join('master_areas', 'job_areas.area_id', '=', 'master_areas.id')
            ->join('job_listings', 'job_areas.job_id', '=', 'job_listings.id')
            ->join('job_job_types', 'job_listings.id', '=', 'job_job_types.job_id')
            ->join('master_job_types', 'job_job_types.job_type_id', '=', 'master_job_types.id')
            ->where('master_areas.prefecture', '沖縄県')
            ->where('master_areas.is_active', 1)
            ->where('master_job_types.is_active', 1)
            ->whereNull('job_listings.deleted_at')
            ->whereNotNull('job_listings.email_verified_at')
            ->where('job_listings.is_admin_hidden', 0)
            ->where(function ($q) {
                $q->whereNull('job_listings.expires_at')
                  ->orWhere('job_listings.expires_at', '>', now());
            })
            ->select('master_areas.slug as area_slug', 'master_job_types.slug as job_type_slug')
            ->distinct()
            ->get();

        return response()
            ->view('sitemap', compact('articles', 'areas', 'jobs', 'areaJobTypeCombos'))
            ->header('Content-Type', 'application/xml');
    }
}
