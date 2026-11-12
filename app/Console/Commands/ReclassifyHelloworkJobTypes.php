<?php

namespace App\Console\Commands;

use App\Models\Job;
use App\Models\MasterJobType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * 「介護職員（施設）」に入ってしまったハローワーク求人のうち、ケアマネ・サービス提供責任者・看護の求人を正しい職種に移す。
 * 以前の判定では「介護支援専門員」「看護職員」などが「介護」に吸われて介護職員になっていたため(2026-11 修正)。
 * 元のHW職種名は保存していないので、seo_title(「事業所名 職種求人｜市町村｜Care Entry」)の職種部分で判定する。
 * 誤判定を増やさないよう、移す先はこの3職種に限る。
 */
class ReclassifyHelloworkJobTypes extends Command
{
    protected $signature = 'hellowork:reclassify-job-types {--dry-run : 移さずに件数と内容だけ表示する}';
    protected $description = '介護職員（施設）に入っているHW求人のうち、ケアマネ・サ責・看護を正しい職種に移す';

    /** 職種部分にこの言葉があれば移す(上から順に判定) */
    private const RULES = [
        '介護支援専門員'     => 'ケアマネジャー（介護支援専門員）',
        'ケアマネ'           => 'ケアマネジャー（介護支援専門員）',
        'サービス提供責任者' => 'サービス提供責任者',
        'サ責'               => 'サービス提供責任者',
        '看護'               => '看護師（福祉施設勤務）',
    ];

    public function handle(): int
    {
        $dry   = (bool) $this->option('dry-run');
        $types = MasterJobType::active()->get()->keyBy('name');
        $care  = $types['介護職員（施設）'] ?? null;
        if (! $care) {
            $this->error('介護職員（施設）が見つかりません');
            return self::FAILURE;
        }

        $jobs = Job::where('source', 'hellowork')
            ->whereHas('jobJobTypes', fn($q) => $q->where('job_type_id', $care->id))
            ->get(['id', 'company_name', 'seo_title', 'title']);

        $moved = [];
        foreach ($jobs as $job) {
            $part = $this->jobPart($job);
            // 「介護・看護職員」のように介護も含むものは介護職員のままにする(看護のみ)
            foreach (self::RULES as $keyword => $name) {
                if (! str_contains($part, $keyword)) {
                    continue;
                }
                if ($keyword === '看護' && str_contains($part, '介護')) {
                    break;
                }
                $to = $types[$name] ?? null;
                if ($to) {
                    $moved[] = [$job->id, $name, $part];
                    if (! $dry) {
                        DB::table('job_job_types')
                            ->where('job_id', $job->id)->where('job_type_id', $care->id)
                            ->update(['job_type_id' => $to->id]);
                    }
                }
                break;
            }
        }

        foreach (collect($moved)->groupBy(1) as $name => $rows) {
            $this->info(($dry ? '[試算] ' : '') . "{$name}: " . count($rows) . '件');
            foreach ($rows->take(5) as [$id, , $part]) {
                $this->line("   #{$id} {$part}");
            }
        }
        $this->info(($dry ? '[試算] 移す対象' : '移した件数') . ': ' . count($moved) . '件 / 介護職員（施設）のHW求人 ' . $jobs->count() . '件中');

        if (! $dry && count($moved) > 0) {
            $this->line('市町村ページの職種の内訳は、集計キャッシュが切れる1時間以内に反映されます。');
        }

        return self::SUCCESS;
    }

    /** seo_title から事業所名と「｜市町村｜Care Entry」を除いた職種部分 */
    private function jobPart(Job $job): string
    {
        $t = (string) ($job->seo_title ?: $job->title);
        $t = explode('｜', $t)[0];
        foreach ([$job->company_name, str_replace('　', ' ', (string) $job->company_name)] as $name) {
            if ($name !== '') {
                $t = str_replace($name, '', $t);
            }
        }
        return trim($t);
    }
}
