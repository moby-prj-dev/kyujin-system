<?php

namespace App\Http\Controllers;

use App\Services\RecommendationImpressionLogger;
use Illuminate\Http\Request;

/**
 * 送客枠のカードが画面に映った記録(navigator.sendBeacon から呼ばれる)。
 * items[] は「slot:求人ID」(例: pr_job:12740)。計測の失敗で画面を妨げないよう常に 204 を返す
 */
class ImpressionTrackController extends Controller
{
    private const SLOTS = ['pr_job', 'direct_job'];
    private const PAGES = ['lp', 'article', 'top', 'search'];
    private const MAX_ITEMS = 30;

    public function store(Request $request, RecommendationImpressionLogger $logger)
    {
        $page = (string) $request->input('page');
        if (! in_array($page, self::PAGES, true)) {
            return response()->noContent();
        }

        $items = [];
        foreach (array_slice((array) $request->input('items', []), 0, self::MAX_ITEMS) as $raw) {
            if (! is_string($raw) || ! preg_match('/^(pr_job|direct_job):(\d{1,10})$/', $raw, $m)) {
                continue;
            }
            $items[$raw] = ['slot' => $m[1], 'job_id' => (int) $m[2]];
        }

        $logger->record(array_values($items), $page);

        return response()->noContent();
    }
}
