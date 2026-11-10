<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentArticle;
use App\Models\MasterArea;
use App\Models\MasterJobType;
use App\Services\ArticleGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $articles = ContentArticle::with(['area', 'jobType'])
            ->orderByDesc('updated_at')
            ->get();

        // 閲覧数(ボット・運営者は記録時点で除外済み)
        $views30    = DB::table('article_views')->where('viewed_at', '>=', now()->subDays(30))
            ->selectRaw('article_id, COUNT(*) c')->groupBy('article_id')->pluck('c', 'article_id')->map(fn($c) => (int) $c)->all();
        $viewsTotal = DB::table('article_views')
            ->selectRaw('article_id, COUNT(*) c')->groupBy('article_id')->pluck('c', 'article_id')->map(fn($c) => (int) $c)->all();

        $sort = $request->query('sort') === 'views' ? 'views' : 'updated';
        if ($sort === 'views') {
            $articles = $articles->sortByDesc(fn($a) => [$views30[$a->id] ?? 0, $viewsTotal[$a->id] ?? 0])->values();
        }

        $areas    = MasterArea::active()->where('prefecture', '沖縄県')->orderBy('sort_order')->get();
        $jobTypes = MasterJobType::active()->orderBy('sort_order')->get()->groupBy('category');

        return view('admin.articles.index', compact('articles', 'areas', 'jobTypes', 'views30', 'viewsTotal', 'sort'));
    }

    public function generate(Request $request, ArticleGeneratorService $service)
    {
        $request->validate([
            'keywords' => ['required', 'string', 'max:300'],
            'category' => ['required', 'in:industry,job_type,area,qualification,beginner,practical'],
            'slug'     => ['nullable', 'string', 'max:100', 'regex:/^[a-z0-9\-]+$/'],
            'area_id'  => ['nullable', 'integer', 'exists:master_areas,id'],
            'job_type_id' => ['nullable', 'integer', 'exists:master_job_types,id'],
        ], [
            'keywords.required' => 'キーワードを入力してください。',
            'category.required' => 'カテゴリーを選択してください。',
            'slug.regex'        => 'スラッグは半角英数字とハイフンのみ使用できます。',
        ]);

        $keywords = array_values(array_filter(array_map('trim', explode(',', $request->keywords))));
        $slug     = $request->filled('slug')
            ? $request->slug
            : $request->category . '-' . now()->format('ymd-His');

        try {
            $article = $service->generateFromInput(
                slug:       $slug,
                keywords:   $keywords,
                category:   $request->category,
                areaId:     $request->filled('area_id')     ? (int) $request->area_id     : null,
                jobTypeId:  $request->filled('job_type_id') ? (int) $request->job_type_id : null,
            );

            return redirect()->route('admin.articles.index')
                ->with('success', "「{$article->title}」を生成しました。");
        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors(['generate' => '記事の生成に失敗しました：' . $e->getMessage()]);
        }
    }

    public function edit(ContentArticle $article)
    {
        $areas    = MasterArea::active()->where('prefecture', '沖縄県')->orderBy('sort_order')->get();
        $jobTypes = MasterJobType::active()->orderBy('sort_order')->get()->groupBy('category');
        return view('admin.articles.edit', compact('article', 'areas', 'jobTypes'));
    }

    public function update(Request $request, ContentArticle $article)
    {
        $request->validate([
            'title'            => ['required', 'string', 'max:100'],
            'h1'               => ['nullable', 'string', 'max:100'],
            'meta_description' => ['nullable', 'string', 'max:200'],
            'body'             => ['required', 'string'],
            'image_url'        => ['nullable', 'url', 'max:500'],
            'area_id'          => ['nullable', 'integer', 'exists:master_areas,id'],
            'job_type_id'      => ['nullable', 'integer', 'exists:master_job_types,id'],
            'published_at'     => ['nullable', 'date'],
        ]);

        $article->update($request->only([
            'title', 'h1', 'meta_description', 'body',
            'image_url', 'area_id', 'job_type_id', 'published_at',
        ]));

        return redirect()->route('admin.articles.index')
            ->with('success', "「{$article->title}」を更新しました。");
    }

    public function destroy(ContentArticle $article)
    {
        $title = $article->title;
        $article->delete();

        return back()->with('success', "「{$title}」を削除しました。");
    }
}
