{{-- 求人一覧のカード1件分(一覧・周辺エリアの求人で共用)。$job を渡す --}}
                    @php
                        $isHw = $job->source === 'hellowork';
                        // NEWバッジは自社(care_entry)求人のみ・HW求人には表示しない
                        $isNew = !$isHw && $job->created_at && $job->created_at->gte(now()->subDays(7));
                        // スタンダードプラン(露出拡大)のカード。ステマ規制対応でPR表記は必須(掲載期限切れ等の対象外求人は通常表示)
                        $isPr = !$isHw && $job->isPrEligible();
                    @endphp
                    <div class="job-card{{ $isPr ? ' job-card--pr' : '' }}">
                        @if($isPr)
                            <div class="job-card__pr-head">
                                <span class="job-card__pr-label"><i class="bi bi-star-fill"></i> おすすめ求人</span>
                                <span class="job-card__pr-mark">PR</span>
                            </div>
                        @endif
                        <p class="job-card__company">{{ $job->company_name }}</p>
                        <p class="job-card__title">
                            <a href="{{ route('lp.show', $job->token) }}"@if($isPr) data-track="pr_job" data-target-job="{{ $job->id }}"@endif>
                                {{ $job->displayTitle() }}
                            </a>
                        </p>
                        <div class="job-card__tags">
                            @if($isNew)
                                <span class="job-card__tag" style="background:#e53935;color:#fff;font-size:.72rem;font-weight:800;padding:3px 10px;border-radius:4px;letter-spacing:.5px;">NEW</span>
                            @endif
                            @foreach($job->jobAreas->take(2) as $ja)
                                @if($ja->area)
                                    <span class="job-card__tag job-card__tag--area">
                                        <i class="bi bi-geo-alt-fill"></i> {{ $ja->area->name }}
                                    </span>
                                @endif
                            @endforeach
                            @foreach($job->jobJobTypes->take(2) as $jt)
                                @if($jt->jobType)
                                    <span class="job-card__tag job-card__tag--job-type">{{ $jt->jobType->name }}</span>
                                @endif
                            @endforeach
                        </div>
                        @if($isPr && ($prTags = $job->highlightTags(3)))
                            <div class="job-card__pr-tags">
                                @foreach($prTags as $tag)
                                    <span><i class="bi bi-check2"></i> {{ $tag }}</span>
                                @endforeach
                            </div>
                        @endif
                        @if($job->salary_type && $job->salary_min)
                            <p class="job-card__salary">
                                <span>{{ $job->salaryText() }}</span>
                            </p>
                        @endif
                        <div class="job-card__actions">
                            <a href="{{ route('lp.show', $job->token) }}" class="btn-card-detail"@if($isPr) data-track="pr_job" data-target-job="{{ $job->id }}"@endif>
                                <i class="bi bi-arrow-right-circle"></i>詳細を見る
                            </a>
                        </div>
                        @if($isPr)
                            <p class="job-card__pr-note"><i class="bi bi-chat-dots-fill"></i> LINE・フォームから事業所に直接応募できます</p>
                        @endif
                    </div>
