{{--
    おすすめ求人(PR)カード ※スタンダードプラン「露出拡大」枠
    - 有料掲載のためステマ規制に従い必ず「PR」表記を付ける
    - 他ページのCSSに依存しないようインラインstyleで完結
    引数:
      $jobs    求人コレクション(jobAreas.area / jobJobTypes.jobType を eager load 済み)
      $heading 見出し(省略可)
      $wrap    'section-card'(LPページ) / 'box'(記事ページ) / 'none'(見出しは呼び出し側)
      $layout  'list'(縦並び) / 'grid'(トップページ用カードグリッド)
--}}
@php
    $jobs    = $jobs ?? collect();
    $heading = $heading ?? 'おすすめ求人';
    $wrap    = $wrap ?? 'section-card';
    $layout  = $layout ?? 'list';
    $prBadge = '<span style="display:inline-block;font-size:0.62rem;font-weight:700;color:#6b7280;background:#fff;border:1px solid #c4c9d2;border-radius:3px;padding:0 5px;line-height:1.55;letter-spacing:0.04em;vertical-align:middle;">PR</span>';
@endphp
@if($jobs->isNotEmpty())
    @if($wrap === 'section-card')
    <div class="section-card">
        <div class="section-title"><i class="bi bi-star-fill"></i> {{ $heading }} {!! $prBadge !!}</div>
    @elseif($wrap === 'box')
    <div style="margin-top:2rem;padding:1.25rem;background:#fffdf6;border:1px solid #f1e3b8;border-radius:8px;">
        <p style="font-weight:800;font-size:1rem;color:#1a1a2e;margin-bottom:0.85rem;">
            <i class="bi bi-star-fill me-1" style="color:#e0a100;"></i>{{ $heading }} {!! $prBadge !!}
        </p>
    @endif

    @if($layout === 'grid')<div class="row g-3">@else<div class="d-flex flex-column gap-2 mt-2">@endif
        @foreach($jobs as $recJob)
            @php
                $recAreas   = $recJob->jobAreas->map(fn($ja) => $ja->area?->name)->filter()->take(2)->implode('・');
                $recJobType = $recJob->jobJobTypes->first()?->jobType?->name;
                $recSalary  = $recJob->salaryText();
            @endphp
            @if($layout === 'grid')<div class="col-sm-6 col-lg-4">@endif
            <a href="{{ route('lp.show', $recJob->token) }}"
               style="display:block;{{ $layout === 'grid' ? 'height:100%;' : '' }}padding:12px 14px;background:#fff;border:1px solid #e5e9f0;border-radius:8px;text-decoration:none;color:#1a1a2e;transition:.15s;"
               onmouseover="this.style.borderColor='#1a73e8';this.style.boxShadow='0 2px 8px rgba(26,115,232,0.15)';"
               onmouseout="this.style.borderColor='#e5e9f0';this.style.boxShadow='none';">
                <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;">
                    {!! $prBadge !!}
                    @if($recJob->company_name)<span style="font-size:0.75rem;color:#666;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $recJob->company_name }}</span>@endif
                </div>
                <div style="font-weight:700;font-size:0.92rem;line-height:1.4;margin-bottom:4px;">{{ $recJob->seo_title ?: $recJob->title }}</div>
                <div style="font-size:0.76rem;color:#666;line-height:1.5;">
                    @if($recAreas)<i class="bi bi-geo-alt-fill me-1"></i>{{ $recAreas }}@endif
                    @if($recJobType)@if($recAreas)・@endif{{ $recJobType }}@endif
                </div>
                @if($recSalary !== '')
                <div style="font-size:0.8rem;font-weight:700;color:#d35400;margin-top:2px;">
                    <i class="bi bi-currency-yen"></i>{{ $recSalary }}
                </div>
                @endif
            </a>
            @if($layout === 'grid')</div>@endif
        @endforeach
    </div>
    <p style="font-size:0.68rem;color:#999;margin:0.5rem 0 0;">※「PR」表示の求人は掲載事業所の広告枠として表示しています。</p>

    @if($wrap === 'section-card' || $wrap === 'box')
    </div>
    @endif
@endif
