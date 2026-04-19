<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\Model\ReplyMessageRequest;
use LINE\Clients\MessagingApi\Model\TextMessage;
use LINE\Parser\EventRequestParser;
use LINE\Parser\Exception\InvalidSignatureException;
use LINE\Webhook\Model\FollowEvent;
use LINE\Webhook\Model\MessageEvent;
use LINE\Webhook\Model\TextMessageContent;

class LineWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $channelSecret = config('line.channel_secret');
        $signature     = $request->header('X-Line-Signature', '');

        try {
            $events = EventRequestParser::parseEventRequest(
                $request->getContent(),
                $channelSecret,
                $signature
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
        $replyToken = $event->getReplyToken();

        $api->replyMessage(new ReplyMessageRequest([
            'replyToken' => $replyToken,
            'messages'   => [
                new TextMessage([
                    'type' => 'text',
                    'text' => "友だち追加ありがとうございます！\n求人LPの「LINEで応募する」ボタンから応募手続きができます。",
                ]),
            ],
        ]));
    }

    private function handleMessage(MessagingApiApi $api, MessageEvent $event): void
    {
        $replyToken = $event->getReplyToken();

        $api->replyMessage(new ReplyMessageRequest([
            'replyToken' => $replyToken,
            'messages'   => [
                new TextMessage([
                    'type' => 'text',
                    'text' => "応募はこちらの求人ページからお手続きください。\nご不明な点はお電話にてお問い合わせください。",
                ]),
            ],
        ]));
    }

    private function buildApi(): MessagingApiApi
    {
        $token  = config('line.channel_access_token');
        $config = \LINE\Clients\MessagingApi\Configuration::getDefaultConfiguration()
            ->setAccessToken($token);

        return new MessagingApiApi(
            new \GuzzleHttp\Client(),
            $config
        );
    }
}
