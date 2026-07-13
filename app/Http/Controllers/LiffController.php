<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use App\Models\LineApplicationDetail;
use App\Models\LineChatSession;
use App\Models\LineEntryToken;
use App\Services\LineMessageBuilder;
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
        // LINE応募機能はスタンダードプラン限定
        if (!$job->isStandard()) {
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

    public function startConversation(Request $request, string $token)
    {
        $entryToken = LineEntryToken::where('token', $token)->firstOrFail();
        if (!$entryToken->isValid()) {
            return response()->json(['error' => 'expired'], 410);
        }

        $data = $request->validate([
            'line_user_id'      => ['required', 'string', 'max:64'],
            'line_display_name' => ['nullable', 'string', 'max:200'],
        ]);

        $lineUserId = $data['line_user_id'];

        $job = $entryToken->job;
        $job->load(['jobAreas.area', 'jobJobTypes.jobType', 'jobEmploymentTypes.employmentType', 'jobConditions.condition']);
        $searchConditions = $entryToken->search_conditions_json ?? [];

        DB::transaction(function () use ($lineUserId, $job, $token, $searchConditions, $entryToken) {
            LineChatSession::where('line_user_id', $lineUserId)->delete();
            LineChatSession::create([
                'line_user_id'           => $lineUserId,
                'step'                   => LineChatSession::STEP_CONFIRMING,
                'job_id'                 => $job->id,
                'entry_token'            => $token,
                'search_conditions_json' => $searchConditions,
                'expires_at'             => now()->addMinutes(30),
            ]);
            $entryToken->markAsUsed($lineUserId);
        });

        try {
            $config = \LINE\Clients\MessagingApi\Configuration::getDefaultConfiguration()
                ->setAccessToken(config('line.channel_access_token'));
            $api = new \LINE\Clients\MessagingApi\Api\MessagingApiApi(new \GuzzleHttp\Client(), $config);
            $api->pushMessage(new \LINE\Clients\MessagingApi\Model\PushMessageRequest([
                'to'       => $lineUserId,
                'messages' => [LineMessageBuilder::confirmation($job, $searchConditions)],
            ]));
        } catch (\Throwable $e) {
            Log::warning('LINE push failed: ' . $e->getMessage());
            return response()->json([
                'error'   => 'push_failed',
                'message' => '応募確認の送信に失敗しました。Care Entryを友だち追加してから再度お試しください。',
            ], 500);
        }

        return response()->json(['success' => true]);
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

            $isToken     = (bool) preg_match('/^[A-Za-z0-9]{16,128}$/', $path);
            $isAutoSend  = (bool) preg_match('/^auto-send\/[A-Za-z0-9]{16,128}$/', $path);

            if ($path !== '' && ($isToken || $isAutoSend)) {
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
            'email'          => ['nullable', 'email', 'max:200'],
            'appeal_message' => ['nullable', 'string', 'max:1000'],
            'screener_answers_json' => ['nullable', 'string', 'max:2000'],
            'line_user_id'   => ['required', 'string'],
            'line_display_name' => ['nullable', 'string'],
        ], [
            'applicant_name.required' => 'お名前を入力してください。',
            'phone.required'          => '電話番号を入力してください。',
            'phone.regex'             => '電話番号はハイフンなしの数字10〜11桁で入力してください。',
            'email.email'             => 'メールアドレスの形式が正しくありません。',
            'appeal_message.max'      => '志望動機・自己PRは1000文字以内で入力してください。',
            'line_user_id.required'   => 'LINEログインが完了していません。',
        ]);

        // 選考質問がある場合、回答が全部揃っているかチェック
        $screenerAnswers = null;
        if (!empty($job->screener_questions)) {
            $parsed = json_decode($request->input('screener_answers_json', '[]'), true) ?: [];
            if (count($parsed) < count($job->screener_questions)) {
                return response()->json(['error' => 'screener_incomplete', 'message' => '選考質問への回答が不足しています。'], 422);
            }
            $screenerAnswers = collect($job->screener_questions)->map(fn($sq, $i) => [
                'q' => $sq['q'],
                'a' => mb_substr((string)($parsed[$i]['a'] ?? ''), 0, 200),
            ])->all();
        }

        $application = null;
        DB::transaction(function () use ($request, $job, &$application, $screenerAnswers) {
            $application = Application::create([
                'job_id'           => $job->id,
                'application_type' => Application::TYPE_LINE,
                'applicant_name'   => $request->applicant_name,
                'phone'            => $request->phone,
                'email'            => $request->email ?: null,
                'status'           => Application::STATUS_RECEIVED,
                'applied_at'       => now(),
            ]);

            LineApplicationDetail::create([
                'application_id'   => $application->id,
                'line_user_id'     => $request->line_user_id,
                'line_session_id'  => $request->line_session_id,
                'appeal_message'   => $request->input('appeal_message') ?: null,
                'screener_answers' => $screenerAnswers,
                'raw_answers_json' => [
                    'display_name' => $request->line_display_name,
                ],
            ]);

            $this->notifyEmployer($job, $application);
        });

        // 応募者に応募控えメール送信(任意入力・失敗しても応募自体は成立)
        if ($application && $request->filled('email')) {
            try {
                \Illuminate\Support\Facades\Mail::to($request->email)
                    ->send(new \App\Mail\ApplicantConfirmationMail($job, $application));
            } catch (\Throwable $e) {
                Log::warning('応募者控えメール送信失敗: ' . $e->getMessage());
            }
        }

        return response()->json(['redirect' => route('liff.thanks', ['token' => $token])]);
    }

    public function thanks(string $token)
    {
        $job = Job::where('token', $token)->firstOrFail();
        return view('lp.liff_thanks', compact('job'));
    }

    private function notifyEmployer(Job $job, Application $application): void
    {
        $recipients = $job->notificationEmails();
        if (empty($recipients)) {
            return;
        }

        try {
            \Illuminate\Support\Facades\Mail::raw(
                implode("\n", array_filter([
                    "【求人応募通知】LINEから新しい応募が届きました",
                    "",
                    "■ 応募者情報",
                    "氏名：{$application->applicant_name}",
                    "電話：{$application->phone}",
                    $application->email ? "メール：{$application->email}" : null,
                    "応募方法：LINE",
                    "",
                    $application->lineDetail?->appeal_message
                        ? "■ 志望動機・自己PR\n{$application->lineDetail->appeal_message}\n"
                        : null,
                    !empty($application->lineDetail?->screener_answers)
                        ? "■ 選考質問への回答\n" . collect($application->lineDetail->screener_answers)->map(fn($qa, $i) => "Q" . ($i + 1) . ". {$qa['q']}\n → {$qa['a']}")->join("\n") . "\n"
                        : null,
                    "■ 求人管理ページ",
                    url('/jobs/' . $job->token),
                ])),
                fn($message) => $message
                    ->to($recipients)
                    ->subject('【求人応募通知】LINEから新しい応募が届きました')
            );
        } catch (\Exception $e) {
            Log::warning('LINE応募通知メール送信失敗: ' . $e->getMessage());
        }
    }
}
