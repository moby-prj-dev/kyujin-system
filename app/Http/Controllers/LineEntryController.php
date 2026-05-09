<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\LineEntryToken;
use Illuminate\Http\Request;

class LineEntryController extends Controller
{
    public function entry(Request $request, string $token)
    {
        $job = Job::where('token', $token)
            ->where('status', 'active')
            ->whereNotNull('email_verified_at')
            ->where('is_admin_hidden', false)
            ->firstOrFail();

        $searchConditions = [
            'area_ids'            => array_values(array_filter(array_map('intval', (array) $request->input('area_ids', [])))),
            'job_type_ids'        => array_values(array_filter(array_map('intval', (array) $request->input('job_type_ids', [])))),
            'employment_type_ids' => array_values(array_filter(array_map('intval', (array) $request->input('employment_type_ids', [])))),
            'condition_ids'       => array_values(array_filter(array_map('intval', (array) $request->input('condition_ids', [])))),
            'appeal_ids'          => array_values(array_filter(array_map('intval', (array) $request->input('appeal_ids', [])))),
        ];

        $hasConditions = !empty($searchConditions['area_ids'])
            || !empty($searchConditions['job_type_ids'])
            || !empty($searchConditions['employment_type_ids'])
            || !empty($searchConditions['condition_ids']);

        if (!$hasConditions) {
            return redirect()->route('lp.apply', ['token' => $job->token, 'via' => 'line']);
        }

        $entryToken = LineEntryToken::create([
            'job_id'                 => $job->id,
            'search_conditions_json' => $searchConditions,
        ]);

        $oaUrl = config('line.oa_url');

        if (empty($oaUrl)) {
            return redirect()->route('lp.show', $job->token);
        }

        $lineUrl = $this->buildOaMessageUrl($oaUrl, $entryToken->token);
        return redirect()->away($lineUrl);
    }

    private function buildOaMessageUrl(string $oaUrl, string $entryTokenValue): string
    {
        if (preg_match('/@([A-Za-z0-9_-]+)/', $oaUrl, $m)) {
            $basicId = '@' . $m[1];
        } else {
            $basicId = '@' . ltrim(trim($oaUrl), '@');
        }

        return 'https://line.me/R/oaMessage/' . $basicId . '/?' . urlencode('apply:' . $entryTokenValue);
    }
}
