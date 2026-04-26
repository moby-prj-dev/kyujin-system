<?php

namespace App\Console\Commands;

use App\Models\Job;
use App\Models\MasterArea;
use App\Models\MasterEmploymentType;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ImportHelloworkJobs extends Command
{
    protected $signature   = 'hellowork:import';
    protected $description = 'ハローワーク求人JSONをDBにインポートする';

    private const JSON_PATH = 'scripts/hellowork_okinawa_kaigo.json';

    public function handle(): int
    {
        $path = base_path(self::JSON_PATH);
        if (! file_exists($path)) {
            $this->error('JSONファイルが見つかりません: ' . $path);
            return self::FAILURE;
        }

        $allData = json_decode(file_get_contents($path), true);
        $areas   = MasterArea::where('prefecture', '沖縄県')->get();
        $empTypes = MasterEmploymentType::all();
        $today   = now()->startOfDay();

        $created = $updated = $skipped = 0;
        $bar = $this->output->createProgressBar(count($allData));
        $bar->start();

        foreach ($allData as $data) {
            $hwJobNo = $data['求人番号'] ?? null;
            if (! $hwJobNo) { $skipped++; $bar->advance(); continue; }

            // 期限切れはスキップ
            $expiresAt = null;
            if (! empty($data['紹介期限日'])) {
                try {
                    $expiresAt = Carbon::createFromFormat('Y年n月j日', $data['紹介期限日']);
                    if ($expiresAt->lt($today)) { $skipped++; $bar->advance(); continue; }
                } catch (\Exception $e) {}
            }

            [$salaryType, $salaryMin, $salaryMax] = $this->parseSalary($data['賃金'] ?? '');

            $hwUrl = 'https://www.hellowork.mhlw.go.jp/kensaku/GECA110010.do'
                   . '?screenId=GECA110010&action=dispDetailBtn'
                   . '&kJNo=' . str_replace('-', '', $hwJobNo)
                   . '&kJKbn=1&fullPart=1';

            $isNew = ! Job::where('hw_job_no', $hwJobNo)->exists();

            $job = Job::updateOrCreate(
                ['hw_job_no' => $hwJobNo],
                [
                    'source'               => 'hellowork',
                    'hw_job_url'           => $hwUrl,
                    'company_name'         => mb_substr($data['事業所名'] ?? '', 0, 100),
                    'title'                => mb_substr($data['職種'] ?? '', 0, 100),
                    'seo_title'            => mb_substr($data['職種'] ?? '', 0, 100),
                    'description_generated'=> $data['仕事の内容'] ?? '',
                    'salary_type'          => $salaryType,
                    'salary_min'           => $salaryMin,
                    'salary_max'           => $salaryMax,
                    'salary_note'          => $data['賃金'] ?? '',
                    'status'               => Job::STATUS_ACTIVE,
                    'email_verified_at'    => now(),
                    'expires_at'           => $expiresAt,
                    'is_admin_hidden'      => false,
                    'is_monitor'           => false,
                    'is_permanently_free'  => false,
                ]
            );

            // エリア紐付け（再作成）
            $job->jobAreas()->delete();
            $location = $data['就業場所'] ?? $data['就業都道府県'] ?? '';
            $area = $this->matchArea($areas, $location);
            if ($area) {
                $job->jobAreas()->create(['area_id' => $area->id]);
            }

            // 雇用形態紐付け（再作成）
            $job->jobEmploymentTypes()->delete();
            $empType = $this->matchEmploymentType($empTypes, $data['雇用形態'] ?? '');
            if ($empType) {
                $job->jobEmploymentTypes()->create(['employment_type_id' => $empType->id]);
            }

            $isNew ? $created++ : $updated++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("完了: 新規 {$created}件 / 更新 {$updated}件 / スキップ {$skipped}件");

        return self::SUCCESS;
    }

    private function parseSalary(string $salary): array
    {
        preg_match_all('/[\d,]+/', $salary, $matches);
        $nums = array_map(fn($n) => (int) str_replace(',', '', $n), $matches[0] ?? []);
        if (empty($nums)) return [null, null, null];

        $min  = $nums[0];
        $max  = $nums[1] ?? null;
        $type = $min < 10000 ? 'hourly' : 'monthly';

        return [$type, $min, $max];
    }

    private function matchArea($areas, string $location): ?MasterArea
    {
        foreach ($areas as $area) {
            if (str_contains($location, $area->name)) return $area;
        }
        return null;
    }

    private function matchEmploymentType($types, string $empType): ?MasterEmploymentType
    {
        $map = [
            '正社員以外' => '契約社員',
            '正社員'     => '正社員',
            'パート'     => 'パート',
            '有期雇用'   => '契約社員',
            '派遣'       => '派遣社員',
        ];
        foreach ($map as $keyword => $name) {
            if (str_contains($empType, $keyword)) {
                return $types->firstWhere('name', $name);
            }
        }
        return null;
    }
}
