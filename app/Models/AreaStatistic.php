<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaStatistic extends Model
{
    protected $fillable = [
        'prefecture', 'occupation', 'year',
        'monthly_wage', 'annual_special_wage', 'sample_size',
        'source', 'raw_payload', 'fetched_at',
    ];

    protected $casts = [
        'raw_payload' => 'array',
        'fetched_at'  => 'datetime',
    ];

    const SOURCE_ESTAT_WAGE_CENSUS = 'estat_chingin_census';

    /**
     * 沖縄県の代表的な介護関連職種マッピング
     * (key: master_job_types.name の prefix にマッチするキーワード, value: e-Stat の職業分類名)
     */
    public static function occupationKeywordMap(): array
    {
        return [
            '介護職員'         => '介護職員',
            'ホームヘルパー'   => 'ホームヘルパー',
            '介護福祉士'       => '介護職員', // 介護職員カテゴリで代用
            'ケアマネ'         => '介護支援専門員',
            '社会福祉士'       => '社会福祉士',
            '保育士'           => '保育士',
        ];
    }

    /**
     * 求人(または記事)の職種名に最も合う統計レコードを返す
     */
    public static function findForJobTypeName(string $prefecture, ?string $jobTypeName): ?self
    {
        if (empty($jobTypeName)) {
            return self::latestFor($prefecture, '介護職員');
        }
        foreach (self::occupationKeywordMap() as $keyword => $occupation) {
            if (str_contains($jobTypeName, $keyword)) {
                $stat = self::latestFor($prefecture, $occupation);
                if ($stat) return $stat;
            }
        }
        return self::latestFor($prefecture, '介護職員');
    }

    public static function latestFor(string $prefecture, string $occupation): ?self
    {
        return self::where('prefecture', $prefecture)
            ->where('occupation', $occupation)
            ->orderByDesc('year')
            ->first();
    }
}
