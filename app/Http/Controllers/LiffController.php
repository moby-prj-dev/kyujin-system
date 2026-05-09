<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use App\Models\LineApplicationDetail;
use App\Models\LineEntryToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LiffController extends Controller
{
    private function findActiveJob(string $token): Job
    {
        $job = Job::where('token', $token)->where('status', 'active')->firstOrFail();
        if ($job->expires_at && $job->expires_at->isPast()) {
            abort(404);
        }
        if ($job->is_admin_hidden) {
            abort(404);
        }
        return $job;
    }

    public function show(string $token)
    {
        $job    = $this->findActiveJob($token);
        $liffId = config('line.liff_id');

        return view('lp.liff', compact('job', 'liffId'));
    }

    public function autoSend(string $token)
    {
        $entryToken = LineEntryToken::where('token', $token)->firstOrFail();
        if (!$entryToken->isValid()) {
            abort(404);
        }

        $liffId      = config('line.liff_id');
        $oaUrl       = config('line.oa_url');
        $fallbackUrl = !empty($oaUrl) ? $this->buildOaMessageUrl($oaUrl, $entryToken->token) : url('/');

        return view('lp.liff_auto_send', [
            'entryToken'  => $entryToken,
            'liffId'      => $liffId,
            'fallbackUrl' => $fallbackUrl,
        ]);
    }

    private function buildOaMessageUrl(string $oaUrl, string $tokenValue): string
    {
        if (preg_match('/@([A-Za-z0-9_-]+)/', $oaUrl, $m)) {
            $basicId = '@' . $m[1];
        } else {
            $basicId = '@' . ltrim(trim($oaUrl), '@');
        }
        return 'https://line.me/R/oaMessage/' . $basicId . '/?' . urlencode('apply:' . $tokenValue);
    }

    public function callback(Request $request)
    {
        $state = $this->extractLiffState($request);

        if (is_string($state) && $state !== '') {
            $parsed = parse_url($state);
            $path   = ltrim($parsed['path'] ?? '', '/');

            if ($path !== '' && preg_match('/^[A-Za-z0-9]{16,128}$/', $path)) {
                $forward = collect($request->query())
                    ->except(['liff.state', 'liff_state'])
                    ->all();
                if (!empty($parsed['query'])) {
                    parse_str($parsed['query'], $stateQuery);
                    $forward = array_merge($stateQuery, $forward);
                }
                $qs = http_build_query($forward);
                return redirect()->to('/liff/' . $path . ($qs !== '' ? '?' . $qs : ''));
            }
        }

        abort(404);
    }

    private function extractLiffState(Request $request): ?string
    {
        $raw = $request->server('QUERY_STRING') ?? '';
        foreach (explode('&', $raw) as $pair) {
            if ($pair === '') continue;
            [$k, $v] = array_pad(explode('=', $pair, 2), 2, '');
            if (urldecode($k) === 'liff.state') {
                return urldecode($v);
            }
        }
        return null;
    }

    public function store(Request $request, string $token)
    {
        $job = $this->findActiveJob($token);

        $request->validate([
            'applicant_name' => ['required', 'string', 'max:100'],
            'phone'          => ['required', 'regex:/^[0-9]{10,11}$/'],
            'line_user_id'   => ['required', 'string'],
            'line_display_name' => ['nullable', 'string'],
        ], [
            'applicant_name.required' => 'お名前を入力してください。',
            'phone.required'          => '電話番号を入力してください。',
            'phone.regex'             => '電話番号はハイフンなしの数字10〜11桁で入力してください。',
            'line_user_id.required'   => 'LINEログインが完了していません。',
        ]);

        DB::transaction(function () use ($request, $job) {
            $application = Application::create([
                'job_id'           => $job->id,
                'application_type' => Application::TYPE_LINE,
                'applicant_name'   => $request->applicant_name,
                'phone'            => $request->phone,
                'email'            => null,
                'status'           => Application::STATUS_RECEIVED,
                'applied_at'       => now(),
            ]);

            LineApplicationDetail::create([
                'application_id' => $application->id,
                'line_user_id'   => $request->line_user_id,
                'line_session_id' => $request->line_session_id,
                'raw_answers_json' => [
                    'display_name' => $request->line_display_name,
                ],
            ]);

            $this->notifyEmployer($job, $application);
        });

        return response()->json(['redirect' => route('liff.thanks', ['token' => $token])]);
    }

    public function thanks(string $token)
    {
        $job = Job::where('token', $token)->firstOrFail();
        return view('lp.liff_thanks', compact('job'));
    }

    private function notifyEmployer(Job $job, Application $application): void
    {
        if (empty($job->contact_email)) {
            return;
        }

        try {
            \Illuminate\Support\Facades\Mail::raw(
                implode("\n", [
                    "【求人応募通知】LINEから新しい応募が届きました",
                    "",
                    "■ 応募者情報",
                    "氏名：{$application->applicant_name}",
                    "電話：{$application->phone}",
                    "応募方法：LINE",
                    "",
                    "■ 求人管理ページ",
                    url('/jobs/' . $job->token),
                ]),
                fn($message) => $message
                    ->to($job->contact_email)
                    ->subject('【求人応募通知】LINEから新しい応募が届きました')
            );
        } catch (\Exception $e) {
            Log::warning('LINE応募通知メール送信失敗: ' . $e->getMessage());
        }
    }
}
