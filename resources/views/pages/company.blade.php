@extends('layouts.app')
@section('title', '運営者情報')
@section('content')

<div class="page-header">
    <div class="container">
        <h1><i class="bi bi-building me-2"></i>運営者情報</h1>
        <p>Care Entry（ケア・エントリー）の運営者に関する情報です。</p>
    </div>
</div>

<div class="container">
<div class="row justify-content-center">
<div class="col-lg-8 py-2">

    {{-- ミッションブロック --}}
    <div class="form-section text-center py-4 mb-4" style="background: linear-gradient(135deg, #f0f7ff 0%, #e8f0fe 100%);">
        <div style="font-size:2.5rem; margin-bottom:0.75rem;">🌺</div>
        <h2 style="font-size:1.3rem; font-weight:800; color:#1a73e8; margin-bottom:0.75rem;">
            沖縄の介護現場と、<br>働きたい人をつなぐ。
        </h2>
        <p class="text-muted mb-0" style="font-size:0.93rem; line-height:1.9; max-width:480px; margin:0 auto;">
            Care Entry（ケア・エントリー）は、沖縄の介護・福祉分野に特化した求人プラットフォームです。<br>
            掲載無料・AIによる求人文章の自動生成など、事業者様の採用活動をシンプルにサポートします。
        </p>
    </div>

    {{-- 事業内容カード --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="form-section text-center h-100 py-3">
                <div style="font-size:2rem; color:#1a73e8; margin-bottom:0.5rem;"><i class="bi bi-briefcase-fill"></i></div>
                <div class="fw-bold mb-1" style="font-size:0.95rem;">求人掲載サービス</div>
                <p class="text-muted mb-0" style="font-size:0.82rem; line-height:1.7;">介護・福祉職種に特化した求人情報の掲載および求職者への応募仲介</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-section text-center h-100 py-3">
                <div style="font-size:2rem; color:#1a73e8; margin-bottom:0.5rem;"><i class="bi bi-stars"></i></div>
                <div class="fw-bold mb-1" style="font-size:0.95rem;">AI求人文章生成</div>
                <p class="text-muted mb-0" style="font-size:0.82rem; line-height:1.7;">最新のAI技術を活用し、魅力的な求人タイトル・本文を自動で生成</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-section text-center h-100 py-3">
                <div style="font-size:2rem; color:#1a73e8; margin-bottom:0.5rem;"><i class="bi bi-laptop"></i></div>
                <div class="fw-bold mb-1" style="font-size:0.95rem;">ホームページ作成</div>
                <p class="text-muted mb-0" style="font-size:0.82rem; line-height:1.7;">事業者様向けのホームページ制作・運用サポート</p>
            </div>
        </div>
    </div>

    {{-- 運営者情報テーブル --}}
    <div class="form-section">
        <h5>運営者情報</h5>
        <table class="table table-bordered mb-0" style="font-size:0.93rem;">
            <tbody>
                <tr>
                    <th class="bg-light" style="width:180px; vertical-align:middle;">事業者名</th>
                    <td>沖縄デジタルワークス 代表者 岸本 安史</td>
                </tr>
                <tr>
                    <th class="bg-light" style="vertical-align:middle;">所在地</th>
                    <td>東京都台東区上野1丁目17番6号広小路ビル8F-B</td>
                </tr>
                <tr>
                    <th class="bg-light" style="vertical-align:middle;">電話番号</th>
                    <td><a href="tel:07064019492">070-6401-9492</a></td>
                </tr>
                <tr>
                    <th class="bg-light" style="vertical-align:middle;">メールアドレス</th>
                    <td><a href="mailto:careentry.info@gmail.com">careentry.info@gmail.com</a></td>
                </tr>
                <tr>
                    <th class="bg-light" style="vertical-align:middle;">事業内容</th>
                    <td>本サービスは、介護・福祉分野を中心とした求人情報を掲載し、求職者に提供するサービスです。求職者は求人内容および条件を確認した上で応募することができます。</td>
                </tr>
                <tr>
                    <th class="bg-light" style="vertical-align:middle;">届出</th>
                    <td>本サービスは、特定募集情報等提供事業として届出済みです。</td>
                </tr>
                <tr>
                    <th class="bg-light" style="vertical-align:middle;">注意事項</th>
                    <td>掲載されている求人情報は、各企業から提供された内容に基づいています。その正確性・内容については、各企業の責任により提供されています。</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- 代表者プロフィール --}}
    <div class="form-section">
        <h5>代表者プロフィール</h5>
        <p class="fw-bold mb-3" style="font-size:1rem;">岸本 安史（きしもと やすし）</p>
        <p class="mb-3" style="font-size:0.93rem; line-height:1.9;">
            業務システム開発の経験を約10年積んだのち、2026年に沖縄県豊見城市にて
            「沖縄デジタルワークス」を屋号として個人事業を開業。
            Laravel / PHP を中心としたWebシステム開発を行っています。
        </p>
        <p class="mb-3" style="font-size:0.93rem; line-height:1.9;">
            ケアエントリーは、沖縄の介護・福祉現場と求職者をつなぐ仕組みとして、
            「会員登録不要」「LINEで応募完結」「条件マッチで無駄な応募を減らす」
            という設計思想のもと開発・運営しています。
        </p>
        <p class="mb-0" style="font-size:0.93rem; line-height:1.9;">
            事業者様にとって「気軽に試せて、結果が出たときだけ費用が発生する」
            シンプルな仕組みを目指しています。お気軽にお問い合わせください。
        </p>
    </div>

    {{-- お問い合わせ誘導 --}}
    <div class="form-section text-center py-4">
        <h5 style="font-size:1rem;">お問い合わせ</h5>
        <p class="text-muted mb-3" style="font-size:0.9rem;">
            サービスに関するご質問・ご相談は、お気軽にお問い合わせください。<br>
            平日10:00〜18:00にて対応いたします。
        </p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="{{ route('contact.show') }}" class="btn btn-primary px-4">
                <i class="bi bi-envelope me-1"></i>お問い合わせフォーム
            </a>
        </div>
        <p class="text-muted small mt-3 mb-0">
            お電話でのお問い合わせ：<a href="tel:07064019492">070-6401-9492</a>（平日10:00〜18:00）
        </p>
    </div>

    <div class="text-center mt-3">
        <a href="/" class="text-muted small"><i class="bi bi-arrow-left me-1"></i>トップページへ戻る</a>
    </div>

</div>
</div>
</div>
@endsection
