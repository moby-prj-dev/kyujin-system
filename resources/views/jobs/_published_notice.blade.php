{{-- 公開直後の案内: 掲載内容(特にAIが作成したタイトル・本文)の確認を促す。$job と $ai(['title'=>bool,'body'=>bool]) を渡す --}}
    <div class="alert alert-success mb-4" style="border-left:5px solid #198754;">
        <p class="fw-bold mb-2"><i class="bi bi-check-circle-fill me-1"></i>掲載を開始しました。求人ページの内容をご確認ください。</p>
        @if($ai['title'] || $ai['body'])
            <p class="small mb-3">
                空欄にされた項目は<strong>AIが作成</strong>しています。事実と違う点（給与・休日・手当・資格要件など）や、伝えたいことが足りない場合は、下のボタンから修正してください。修正はいつでも何度でもできます。
            </p>
        @endif
        <div class="bg-white rounded p-3 mb-3" style="border:1px solid #cfe8d8;">
            <p class="small text-muted mb-1">
                求人タイトル
                @if($ai['title'])<span class="badge bg-info text-dark ms-1"><i class="bi bi-stars"></i> AIが作成</span>@endif
            </p>
            <p class="fw-bold mb-3">{{ $job->title }}</p>
            <p class="small text-muted mb-1">
                求人本文
                @if($ai['body'])<span class="badge bg-info text-dark ms-1"><i class="bi bi-stars"></i> AIが作成</span>@endif
            </p>
            <p class="mb-0" style="white-space:pre-wrap;">{{ $job->description_generated }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="#edit-job" class="btn btn-success btn-sm"><i class="bi bi-pencil-square me-1"></i>求人内容を編集する</a>
            <a href="{{ url('/lp/' . $job->token) }}" target="_blank" rel="noopener" class="btn btn-outline-success btn-sm"><i class="bi bi-box-arrow-up-right me-1"></i>公開中の求人ページを見る</a>
        </div>
    </div>
