<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\LineApplicationDetail;
use App\Models\LineChatSession;
use App\Models\LineEntryToken;
use App\Models\MasterAppeal;
use App\Models\MasterArea;
use App\Models\MasterCondition;
use App\Models\MasterEmploymentType;
use App\Services\LineAlternativeJobFinder;
use App\Services\LineMatchService;
use App\Services\LineMessageBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\Model\ReplyMessageRequest;
use LINE\Clients\MessagingApi\Model\TextMessage;
use LINE\Parser\EventRequestParser;
use LINE\Parser\Exception\InvalidSignatureException;
use LINE\Webhook\Model\FollowEvent;
use LINE\Webhook\Model\GroupSource;
use LINE\Webhook\Model\MessageEvent;
use LINE\Webhook\Model\TextMessageContent;
use LINE\Webhook\Model\UserSource;

class LineWebhookController extends Controller
{
    public function handle(Request $request)
    {
        try {
            $events = EventRequestParser::parseEventRequest(
                $request->getContent(),
                config('line.channel_secret'),
                $request->header('X-Line-Signature', '')
            );
        } catch (InvalidSignatureException $e) {
            Log::warning('LINE Webhook 署名検証失敗: ' . $e->getMessage());
            return response('Invalid signature', 400);
        } catch (\Exception $e) {
            Log::error('LINE Webhook パース失敗: ' . $e->getMessage());
            return response('Bad request', 400);
        }

        $api = $this->buildApi();

        foreach ($events->getEvents() as $event) {
            try {
                if ($event instanceof FollowEvent) {
                    $this->handleFollow($api, $event);
                } elseif ($event instanceof MessageEvent && $event->getMessage() instanceof TextMessageContent) {
                    $this->handleMessage($api, $event);
                }
            } catch (\Exception $e) {
                Log::error('LINE Webhook イベント処理エラー: ' . $e->getMessage());
            }
        }

        return response('OK', 200);
    }

    private function handleFollow(MessagingApiApi $api, FollowEvent $event): void
    {
        $this->reply($api, $event->getReplyToken(), LineMessageBuilder::follow());
    }

    private function handleMessage(MessagingApiApi $api, MessageEvent $event): void
    {
        /** @var TextMessageContent $content */
        $content = $event->getMessage();
        $text    = trim($content->getText());

        $source = $event->getSource();
        if (!($source instanceof UserSource) && !($source instanceof GroupSource)) {
            return;
        }
        $lineUserId = $source->getUserId();
        $replyToken = $event->getReplyToken();

        if (str_starts_with($text, 'apply:')) {
            $this->handleApplyEntry($api, $replyToken, $lineUserId, substr($text, 6));
            return;
        }

        $session = LineChatSession::findActive($lineUserId);

        if (!$session) {
            $this->reply($api, $replyToken, LineMessageBuilder::noSession());
            return;
        }

        match ($session->step) {
            LineChatSession::STEP_CONFIRMING       => $this->handleConfirming($api, $replyToken, $session, $text),
            LineChatSession::STEP_COLLECTING_NAME  => $this->handleCollectingName($api, $replyToken, $session, $text),
            LineChatSession::STEP_COLLECTING_PHONE => $this->handleCollectingPhone($api, $replyToken, $session, $text),
            default                                => $this->reply($api, $replyToken, LineMessageBuilder::noSession()),
        };
    }

    private function handleApplyEntry(MessagingApiApi $api, string $replyToken, string $lineUserId, string $token): void
    {
        $entryToken = LineEntryToken::where('token', trim($token))->first();

        if (!$entryToken || !$entryToken->isValid()) {
            $this->reply($api, $replyToken, LineMessageBuilder::expiredToken());
            return;
        }

        $job = $entryToken->job;
        $job->load(['jobAreas.area', 'jobJobTypes.jobType', 'jobEmploymentTypes.employmentType', 'jobConditions.condition']);

        LineChatSession::where('line_user_id', $lineUserId)->delete();
        LineChatSession::create([
            'line_user_id'           => $lineUserId,
            'step'                   => LineChatSession::STEP_CONFIRMING,
            'job_id'                 => $job->id,
            'entry_token'            => $entryToken->token,
            'search_conditions_json' => $entryToken->search_conditions_json ?? [],
            'expires_at'             => now()->addMinutes(30),
        ]);

        $entryToken->markAsUsed($lineUserId);

        $this->reply($api, $replyToken, LineMessageBuilder::confirmation($job));
    }

    private function handleConfirming(MessagingApiApi $api, string $replyToken, LineChatSession $session, string $text): void
    {
        $normalized = mb_strtolower(preg_replace('/[\s　]/u', '', $text));
        $isYes      = in_array($normalized, ['はい', 'yes', 'ok', 'ﾊｲ', '○', '◯', '✓']);
        $isReview   = str_contains($text, '確認');

        if (!$isYes && !$isReview) {
            $job = $session->job;
            $job->load(['jobAreas.area', 'jobJobTypes.jobType', 'jobEmploymentTypes.employmentType', 'jobConditions.condition']);
            $this->reply($api, $replyToken, LineMessageBuilder::confirmation($job));
            return;
        }

        $job = $session->job;
        $job->load(['jobAreas.area', 'jobJobTypes.jobType', 'jobEmploymentTypes.employmentType', 'jobConditions.condition']);

        $mismatches = (new LineMatchService())->getMismatches($job, $session->search_conditions_json ?? []);

        if (!empty($mismatches)) {
            $alternatives = (new LineAlternativeJobFinder())->find($job, $session->search_conditions_json ?? []);
            $session->delete();
            $this->reply($api, $replyToken, LineMessageBuilder::mismatch($job, $mismatches, $alternatives));
            return;
        }

        $session->update([
            'step'       => LineChatSession::STEP_COLLECTING_NAME,
            'expires_at' => now()->addMinutes(30),
        ]);

        $this->reply($api, $replyToken, LineMessageBuilder::askName());
    }

    private function handleCollectingName(MessagingApiApi $api, string $replyToken, LineChatSession $session, string $text): void
    {
        $name = trim($text);

        if (mb_strlen($name) < 1 || mb_strlen($name) > 50) {
            $this->reply($api, $replyToken, new TextMessage([
                'type' => 'text',
                'text' => 'お名前を入力してください（50文字以内）。',
            ]));
            return;
        }

        $session->update([
            'applicant_name' => $name,
            'step'           => LineChatSession::STEP_COLLECTING_PHONE,
            'expires_at'     => now()->addMinutes(30),
        ]);

        $this->reply($api, $replyToken, LineMessageBuilder::askPhone());
    }

    private function handleCollectingPhone(MessagingApiApi $api, string $replyToken, LineChatSession $session, string $text): void
    {
        $phone = preg_replace('/[^\d]/', '', $text);

        if (!preg_match('/^\d{10,11}$/', $phone)) {
            $this->reply($api, $replyToken, LineMessageBuilder::phoneInvalid());
            return;
        }

        $job              = $session->job;
        $searchConditions = $session->search_conditions_json ?? [];
        $lineUserId       = $session->line_user_id;
        $applicantName    = $session->applicant_name;
        $entryToken       = $session->entry_token;

        DB::transaction(function () use ($job, $phone, $lineUserId, $applicantName, $entryToken, $searchConditions) {
            $application = Application::create([
                'job_id'           => $job->id,
                'application_type' => Application::TYPE_LINE,
                'applicant_name'   => $applicantName,
                'phone'            => $phone,
                'email'            => null,
                'status'           => Application::STATUS_RECEIVED,
                'applied_at'       => now(),
            ]);

            LineApplicationDetail::create([
                'application_id'               => $application->id,
                'line_user_id'                 => $lineUserId,
                'line_session_id'              => $entryToken,
                'preferred_conditions_summary' => $this->buildConditionsSummary($searchConditions),
                'raw_answers_json'             => $searchConditions,
            ]);

            $this->notifyEmployer($job, $application, $searchConditions);
        });

        $session->delete();

        $this->reply($api, $replyToken, LineMessageBuilder::completed());
    }

    private function buildConditionsSummary(array $conditions): string
    {
        $parts = [];

        if (!empty($conditions['area_ids'])) {
            $names = MasterArea::whereIn('id', $conditions['area_ids'])->pluck('name')->join('、');
            if ($names) $parts[] = "勤務地：{$names}";
        }
        if (!empty($conditions['employment_type_ids'])) {
            $names = MasterEmploymentType::whereIn('id', $conditions['employment_type_ids'])->pluck('name')->join('、');
            if ($names) $parts[] = "雇用形態：{$names}";
        }
        if (!empty($conditions['condition_ids'])) {
            $names = MasterCondition::whereIn('id', $conditions['condition_ids'])->pluck('name')->join('、');
            if ($names) $parts[] = "勤務条件：{$names}";
        }
        if (!empty($conditions['appeal_ids'])) {
            $names = MasterAppeal::whereIn('id', $conditions['appeal_ids'])->pluck('name')->join('、');
            if ($names) $parts[] = "重視ポイント：{$names}";
        }

        return implode("\n", $parts);
    }

    private function notifyEmployer(\App\Models\Job $job, Application $application, array $searchConditions): void
    {
        if (empty($job->contact_email)) {
            return;
        }

        $condSummary = $this->buildConditionsSummary($searchConditions);

        $body = implode("\n", array_filter([
            "【求人応募通知】LINEから新しい応募が届きました",
            "",
            "■ 応募者情報",
            "氏名：{$application->applicant_name}",
            "電話：{$application->phone}",
            "応募方法：LINE",
            "",
            $condSummary ? "■ 応募者の希望条件\n{$condSummary}" : null,
            "",
            "■ 求人管理ページ",
            url('/jobs/' . $job->token),
        ]));

        try {
            \Illuminate\Support\Facades\Mail::raw(
                $body,
                fn($message) => $message
                    ->to($job->contact_email)
                    ->subject('【求人応募通知】LINEから新しい応募が届きました')
            );
        } catch (\Exception $e) {
            Log::warning('LINE応募通知メール送信失敗: ' . $e->getMessage());
        }
    }

    private function reply(MessagingApiApi $api, string $replyToken, $message): void
    {
        $api->replyMessage(new ReplyMessageRequest([
            'replyToken' => $replyToken,
            'messages'   => [$message],
        ]));
    }

    private function buildApi(): MessagingApiApi
    {
        $config = \LINE\Clients\MessagingApi\Configuration::getDefaultConfiguration()
            ->setAccessToken(config('line.channel_access_token'));

        return new MessagingApiApi(new \GuzzleHttp\Client(), $config);
    }
}
