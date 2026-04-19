<?php

namespace App\Services;

use App\Models\Job;

class SeoGeneratorService
{
    /**
     * 求人データからSEOテキストを生成してJobに保存する
     */
    public function generate(Job $job): void
    {
        $area     = $job->jobAreas->map(fn($a) => $a->area->name)->first() ?? '';
        $areaFull = $job->jobAreas->map(fn($a) => $a->area->name)->implode('・');
        $jobType  = $job->jobJobTypes->map(fn($j) => $j->jobType->name)->first() ?? '';
        $employmentType = $job->jobEmploymentTypes->map(fn($e) => $e->employmentType->name)->implode('・');

        $conditions = $job->jobConditions()
            ->with('condition')
            ->get()
            ->pluck('condition.name')
            ->toArray();

        $appeals = $job->jobAppeals()
            ->with('appeal')
            ->get()
            ->pluck('appeal.name')
            ->toArray();

        $conditionStr = implode('・', array_slice($conditions, 0, 3));
        $appealStr    = implode('・', array_slice($appeals, 0, 3));

        // テンプレートをランダム選択（ページ重複率を下げる）
        $templates = $this->getTitleTemplates($area, $jobType, $employmentType, $conditionStr, $appealStr);
        $templateIndex = crc32($area . $jobType) % count($templates);

        $job->seo_title            = $templates[$templateIndex];
        $job->meta_description     = $this->generateMeta($area, $jobType, $employmentType, $conditionStr, $appealStr);
        $job->description_generated = $this->generateDescription($areaFull, $jobType, $employmentType, $conditions, $appeals);
        $job->title                = "{$area}の{$jobType}求人";
        $job->save();
    }

    private function getTitleTemplates(string $area, string $jobType, string $employment, string $conditions, string $appeals): array
    {
        return [
            "【{$area}】{$jobType}募集｜{$conditions}",
            "{$area}で{$jobType}のお仕事｜{$appeals}",
            "{$jobType}求人【{$area}】{$employment}・{$appeals}",
            "【急募】{$area}の{$jobType}｜{$conditions}・{$appeals}",
        ];
    }

    private function generateMeta(string $area, string $jobType, string $employment, string $conditions, string $appeals): string
    {
        return "{$area}エリアで{$jobType}（{$employment}）を募集中。{$conditions}。{$appeals}。LINE応募OK。お気軽にご応募ください。";
    }

    private function generateDescription(string $area, string $jobType, string $employment, array $conditions, array $appeals): string
    {
        $conditionList = implode("\n", array_map(fn($c) => "・{$c}", $conditions));
        $appealList    = implode("\n", array_map(fn($a) => "・{$a}", $appeals));

        return <<<TEXT
{$area}で{$jobType}のお仕事をお探しの方へ

現在、{$area}エリアで{$jobType}（{$employment}）のスタッフを募集しています。

【勤務条件】
{$conditionList}

【この求人のポイント】
{$appealList}

LINE応募またはフォームからお気軽にご応募ください。
応募後、担当者より折り返しご連絡いたします。
TEXT;
    }
}
