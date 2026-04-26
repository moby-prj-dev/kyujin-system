<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class HelloworkJobController extends Controller
{
    private const JSON_PATH = 'scripts/hellowork_okinawa_kaigo.json';
    private const PER_PAGE  = 20;

    public function index(Request $request)
    {
        $path = base_path(self::JSON_PATH);

        if (! file_exists($path)) {
            $jobs = collect();
            $paginator = new LengthAwarePaginator([], 0, self::PER_PAGE);
        } else {
            $all = collect(json_decode(file_get_contents($path), true));

            // キーワード検索
            $keyword = $request->input('q');
            if ($keyword) {
                $all = $all->filter(fn($j) =>
                    str_contains($j['職種'] ?? '', $keyword) ||
                    str_contains($j['事業所名'] ?? '', $keyword) ||
                    str_contains($j['仕事の内容'] ?? '', $keyword) ||
                    str_contains($j['就業場所'] ?? '', $keyword)
                );
            }

            // 雇用形態フィルター
            $employmentType = $request->input('employment_type');
            if ($employmentType) {
                $all = $all->filter(fn($j) =>
                    str_contains($j['雇用形態'] ?? '', $employmentType)
                );
            }

            $total   = $all->count();
            $page    = $request->input('page', 1);
            $items   = $all->slice(($page - 1) * self::PER_PAGE, self::PER_PAGE)->values();

            $paginator = new LengthAwarePaginator(
                $items, $total, self::PER_PAGE, $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        }

        return view('hellowork.index', [
            'jobs'           => $paginator,
            'keyword'        => $keyword ?? '',
            'employmentType' => $employmentType ?? '',
        ]);
    }
}
