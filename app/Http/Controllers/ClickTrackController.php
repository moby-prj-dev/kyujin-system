<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ボタン・送客枠のクリック記録(navigator.sendBeacon から呼ばれる)
 * 計測の失敗で利用者の画面遷移を妨げないよう、常に 204 を返す
 */
class ClickTrackController extends Controller
{
    private const TYPES = ['hw_apply', 'line_apply', 'form_apply', 'pr_job', 'direct_job'];
    private const PAGES = ['lp', 'article', 'top', 'search'];

    public function store(Request $request)
    {
        $type = (string) $request->input('type');
        if (! in_array($type, self::TYPES, true)) {
            return response()->noContent();
        }

        $page = (string) $request->input('page');

        try {
            DB::table('lp_clicks')->insert([
                'type'          => $type,
                'job_id'        => ctype_digit((string) $request->input('job_id')) ? (int) $request->input('job_id') : null,
                'target_job_id' => ctype_digit((string) $request->input('target_job_id')) ? (int) $request->input('target_job_id') : null,
                'page'          => in_array($page, self::PAGES, true) ? $page : null,
                'ip_address'    => $request->ip(),
                'user_agent'    => substr($request->userAgent() ?? '', 0, 500),
                'clicked_at'    => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('lp_click記録失敗: ' . $e->getMessage());
        }

        return response()->noContent();
    }
}
