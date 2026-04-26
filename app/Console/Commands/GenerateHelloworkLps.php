<?php

namespace App\Console\Commands;

use App\Models\Job;
use App\Models\JobJobType;
use App\Models\MasterArea;
use App\Models\MasterEmploymentType;
use App\Models\MasterJobType;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateHelloworkLps extends Command
{
    protected $signature   = 'hellowork:generate-lps {--limit=5 : 生成するLP件数}';
    protected $description = 'ハローワーク求人JSONからAI生成LPを作成する';

    private const JSON_PATH = 'scripts/hellowork_okinawa_kaigo.json';

    public function handle(): int
    {
        $path = base_path(self::JSON_PATH);
        if (! file_exists($path)) {
            $this->error('JSONファイルが見つかりません: ' . $path);
            return self::FAILURE;
        }

        $limit    = (int) $this->option('limit');
        $today    = now()->startOfDay();
        $areas    = MasterArea::where('prefecture', '沖縄県')->get();
        $empTypes = MasterEmploymentType::all();
        $jobTypes = MasterJobType::all();

        // JSON読み込み・期限内のみ・受付年月日降順で上位N件
        $all = collect(json_decode(file_get_contents($path), true))
            ->filter(function ($j) use ($today) {
                if (empty($j['紹介期限日'])) return false;
                try {
                    return Carbon::createFromFormat('Y年n月j日', $j['紹介期限日'])->gte($today);
                } catch (\Exception $e) {
                    return false;
                }
            })
            ->sortByDesc(function ($j) {
                try {
                    return Carbon::createFromFormat('Y年n月j日', $j['受付年月日'] ?? '')->timestamp;
                } catch (\Exception $e) {
                    return 0;
                }
            })
            ->take($limit)
            ->values();

        $this->info("対象: {$all->count()}件 → AI生成開始");

        $targetNos = $all->pluck('求人番号')->filter()->values()->toArray();

        // 今回の対象外HW求人を削除
        $deleted = Job::where('source', 'hellowork')
            ->whereNotIn('hw_job_no', $targetNos)
            ->count();
        Job::where('source', 'hellowork')
            ->whereNotIn('hw_job_no', $targetNos)
            ->delete();
        if ($deleted > 0) $this->line("古いHW求人を{$deleted}件削除");

        $created = $updated = $failed = 0;

        foreach ($all as $data) {
            $hwJobNo = $data['求人番号'] ?? null;
            if (! $hwJobNo) { $failed++; continue; }

            $this->line("生成中: {$data['職種']} / {$data['事業所名']}");

            // Gemini でコンテンツ生成
            $generated = $this->generateContent($data);
            if (! $generated) {
                $this->warn("  → Gemini失敗、スキップ");
                $failed++;
                continue;
            }

            $expiresAt = Carbon::createFromFormat('Y年n月j日', $data['紹介期限日']);
            $hwUrl = 'https://www.hellowork.mhlw.go.jp/kensaku/GECA110010.do'
                   . '?screenId=GECA110010&action=dispDetailBtn'
                   . '&kJNo=' . str_replace('-', '', $hwJobNo)
                   . '&kJKbn=1&fullPart=1';

            [$salaryType, $salaryMin, $salaryMax] = $this->parseSalary($data['賃金'] ?? '');

            $isNew = ! Job::where('hw_job_no', $hwJobNo)->exists();
            $job = Job::updateOrCreate(
                ['hw_job_no' => $hwJobNo],
                [
                    'source'               => 'hellowork',
                    'hw_job_url'           => $hwUrl,
                    'company_name'         => mb_substr($data['事業所名'] ?? '', 0, 100),
                    'title'                => mb_substr($generated['seo_title'], 0, 100),
                    'seo_title'            => mb_substr($generated['seo_title'], 0, 100),
                    'meta_description'     => mb_substr($generated['meta_description'], 0, 320),
                    'description_generated'=> $generated['description'],
                    'salary_type'          => $salaryType,
                    'salary_min'           => $salaryMin,
                    'salary_max'           => $salaryMax,
                    'salary_note'          => $data['賃金'] ?? '',
                    'status'               => Job::STATUS_ACTIVE,
                    'contact_email'        => '',
                    'contact_phone'        => '',
                    'email_verified_at'    => now(),
                    'expires_at'           => $expiresAt,
                    'is_admin_hidden'      => false,
                    'is_monitor'           => false,
                    'is_permanently_free'  => false,
                ]
            );

            $job->jobAreas()->delete();
            $area = $this->matchArea($areas, $data['就業場所'] ?? '');
            if ($area) $job->jobAreas()->create(['area_id' => $area->id]);

            $job->jobEmploymentTypes()->delete();
            $empType = $this->matchEmploymentType($empTypes, $data['雇用形態'] ?? '');
            if ($empType) $job->jobEmploymentTypes()->create(['employment_type_id' => $empType->id]);

            $job->jobJobTypes()->delete();
            $jobType = $this->matchJobType($jobTypes, $data['職種'] ?? '');
            if ($jobType) $job->jobJobTypes()->create(['job_type_id' => $jobType->id]);

            $isNew ? $created++ : $updated++;
            $this->line("  → 完了: {$generated['seo_title']}");

            sleep(1); // Gemini APIレート制限対策
        }

        $this->newLine();
        $this->info("完了: 新規{$created}件 / 更新{$updated}件 / 失敗{$failed}件");

        return self::SUCCESS;
    }

    private function generateContent(array $data): ?array
    {
        $prompt = <<<PROMPT
あなたは介護・福祉求人サイト「Care Entry（ケアエントリー）」のコピーライターです。
以下のハローワーク求人情報をもとに、求職者に魅力的に伝えるランディングページのコンテンツをJSONで生成してください。

【求人情報】
事業所名: {$data['事業所名']}
職種: {$data['職種']}
就業場所: {$data['就業場所']}
賃金: {$data['賃金']}
雇用形態: {$data['雇用形態']}
就業時間: {$data['就業時間']}
休日: {$data['休日']}
仕事の内容: {$data['仕事の内容']}

【出力形式】
以下のキーを持つJSONのみを返してください（説明文不要）:
{
  "seo_title": "SEO最適化されたページタイトル（例：那覇市の介護職員求人 | 社会医療法人敬愛会）60文字以内",
  "meta_description": "このページの説明文（120文字以内）",
  "description": "求職者に伝わる魅力的な仕事紹介文（400〜600文字）。仕事内容・職場環境・働き方などを具体的に。最後にハローワーク経由で応募できる旨を自然に添えること。"
}
PROMPT;

        try {
            $apiKey = config('services.gemini.api_key');
            $model  = config('services.gemini.model', 'gemini-2.5-flash');
            $url    = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

            $client   = new Client(['timeout' => 60]);
            $response = $client->post($url, [
                'headers' => ['content-type' => 'application/json'],
                'json'    => [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => [
                        'maxOutputTokens'  => 1000,
                        'temperature'      => 0.7,
                        'responseMimeType' => 'application/json',
                        'thinkingConfig'   => ['thinkingBudget' => 0],
                    ],
                ],
            ]);

            $raw  = json_decode($response->getBody()->getContents(), true);
            $text = trim($raw['candidates'][0]['content']['parts'][0]['text'] ?? '');
            $data = json_decode($text, true);

            if ($data && isset($data['seo_title'], $data['meta_description'], $data['description'])) {
                return $data;
            }
        } catch (\Exception $e) {
            Log::warning('HW LP Gemini生成失敗: ' . $e->getMessage());
        }

        return null;
    }

    private function parseSalary(string $salary): array
    {
        preg_match_all('/[\d,]+/', $salary, $matches);
        $nums = array_map(fn($n) => (int) str_replace(',', '', $n), $matches[0] ?? []);
        if (empty($nums)) return [null, null, null];
        $min  = $nums[0];
        $max  = $nums[1] ?? null;
        return [$min < 10000 ? 'hourly' : 'monthly', $min, $max];
    }

    private function matchArea($areas, string $location): ?MasterArea
    {
        foreach ($areas as $area) {
            if (str_contains($location, $area->name)) return $area;
        }
        return null;
    }

    private function matchJobType($jobTypes, string $jobTitle): ?MasterJobType
    {
        $map = [
            '介護福祉士'   => '介護福祉士',
            'ケアマネ'     => 'ケアマネジャー（介護支援専門員）',
            '訪問介護'     => 'ホームヘルパー（訪問介護員）',
            'ヘルパー'     => 'ホームヘルパー（訪問介護員）',
            '生活相談'     => '生活相談員',
            '保育士'       => '保育士',
            '児童指導員'   => '児童指導員',
            '生活支援員'   => '生活支援員',
            '就労支援'     => '就労支援員',
            'サービス管理' => 'サービス管理責任者（サビ管）',
            '社会福祉士'   => '社会福祉士',
            '精神保健'     => '精神保健福祉士（PSW）',
            '相談支援'     => '相談支援専門員',
            '看護師'       => '看護師（福祉施設勤務）',
            '理学療法士'   => '理学療法士（PT）',
            '作業療法士'   => '作業療法士（OT）',
            '言語聴覚士'   => '言語聴覚士（ST）',
            '栄養士'       => '管理栄養士（施設）',
            '介護事務'     => '介護事務',
            '医療事務'     => '医療事務',
            '介護'         => '介護職員（施設）',
        ];
        foreach ($map as $keyword => $name) {
            if (str_contains($jobTitle, $keyword)) {
                return $jobTypes->firstWhere('name', $name);
            }
        }
        return null;
    }

    private function matchEmploymentType($types, string $empType): ?MasterEmploymentType
    {
        $map = ['正社員以外' => '契約社員', '正社員' => '正社員', 'パート' => 'パート', '有期雇用' => '契約社員', '派遣' => '派遣社員'];
        foreach ($map as $keyword => $name) {
            if (str_contains($empType, $keyword)) return $types->firstWhere('name', $name);
        }
        return null;
    }
}
