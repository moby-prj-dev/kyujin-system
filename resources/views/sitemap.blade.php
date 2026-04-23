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

    {{-- 記事ページ --}}
    @foreach($articles as $article)
    <url>
        <loc>{{ route('articles.show', $article->slug) }}</loc>
        <lastmod>{{ $article->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach

</urlset>
