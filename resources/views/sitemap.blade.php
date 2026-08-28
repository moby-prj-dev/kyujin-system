<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    {{-- 静的ページ --}}
    <url>
        <loc>{{ url('/') }}</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ route('articles.index') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>{{ route('seo.jobs.okinawa') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>

    {{-- エリア別求人ページ --}}
    @foreach($areas as $area)
    <url>
        <loc>{{ route('seo.jobs.area', $area->slug) }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach

    {{-- エリア×職種別求人ページ (求人が実在する組み合わせのみ) --}}
    @foreach($areaJobTypeCombos as $combo)
    <url>
        <loc>{{ route('seo.jobs.area_jobtype', [$combo->area_slug, $combo->job_type_slug]) }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach

    {{-- 記事ページ --}}
    @foreach($articles as $article)
    <url>
        <loc>{{ route('articles.show', $article->slug) }}</loc>
        <lastmod>{{ $article->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach

    {{-- 求人LPページ --}}
    @foreach($jobs as $job)
    <url>
        <loc>{{ route('lp.show', $job->token) }}</loc>
        <lastmod>{{ $job->updated_at->toAtomString() }}</lastmod>
        <changefreq>{{ $job->source === 'hellowork' ? 'daily' : 'weekly' }}</changefreq>
        <priority>{{ $job->source === 'hellowork' ? '0.6' : '0.8' }}</priority>
    </url>
    @endforeach

</urlset>
