@extends('layouts.app')
@section('title', '特定商取引法に基づく表記')
@section('content')

<div class="page-header">
    <div class="container">
        <h1><i class="bi bi-journal-text me-2"></i>特定商取引法に基づく表記</h1>
        <p>特定商取引法に基づく表示事項です。</p>
    </div>
</div>

<div class="container">
<div class="row justify-content-center">
<div class="col-lg-8 py-2">

    <div class="form-section">
        <table class="table table-bordered mb-0" style="font-size:0.93rem;">
            <tbody>
                <tr>
                    <th class="bg-light" style="width:200px; vertical-align:middle;">事業者名</th>
                    <td>沖縄デジタルワークス 代表者 岸本 安史</td>
                </tr>
                <tr>
                    <th class="bg-light" style="vertical-align:middle;">所在地</th>
                    <td>東京都台東区上野1丁目17番6号広小路ビル8F-B</td>
                </tr>
                <tr>
                    <th class="bg-light" style="vertical-align:middle;">電話番号</th>
                    <td>
                        <a href="tel:07064019492">070-6401-9492</a><br>
                        <span class="text-muted small">受付時間：平日10:00〜18:00（土日祝休）</span>
                    </td>
                </tr>
                <tr>
                    <th class="bg-light" style="vertical-align:middle;">メールアドレス</th>
                    <td><a href="mailto:careentry.info@gmail.com">careentry.info@gmail.com</a></td>
                </tr>
                <tr>
                    <th class="bg-light" style="vertical-align:middle;">サービス名</th>
                    <td>Care Entry（ケア・エントリー）</td>
                </tr>
                <tr>
                    <th class="bg-light" style="vertical-align:middle;">サービス内容</th>
                    <td>介護・福祉分野を中心とした求人情報の掲載および求職者への応募仲介サービス</td>
                </tr>
                <tr>
                    <th class="bg-light" style="vertical-align:middle;">販売価格</th>
                    <td>
                        求人掲載料：無料（モニター企業は掲載開始から3か月間または有効応募3件まで）<br>
                        モニター期間外・期間終了後：有効応募1件につき <strong>3,000円（税別）</strong> の成果報酬が発生します。<br>
                        <span class="text-muted small">※ 有効応募とは、必須項目が入力された重複・スパムでない応募を指します。</span>
                    </td>
                </tr>
                <tr>
                    <th class="bg-light" style="vertical-align:middle;">商品代金以外の<br>必要料金</th>
                    <td>なし（インターネット接続費用等はお客様のご負担となります）</td>
                </tr>
                <tr>
                    <th class="bg-light" style="vertical-align:middle;">支払時期</th>
                    <td>当社からの請求書発行後、指定の期日まで</td>
                </tr>
                <tr>
                    <th class="bg-light" style="vertical-align:middle;">支払方法</th>
                    <td>銀行振込（詳細は請求書記載の口座へ）</td>
                </tr>
                <tr>
                    <th class="bg-light" style="vertical-align:middle;">サービス提供時期</th>
                    <td>メールアドレス確認完了後、即時公開</td>
                </tr>
                <tr>
                    <th class="bg-light" style="vertical-align:middle;">返品・キャンセル</th>
                    <td>
                        サービスの性質上、一度発生した成果報酬のキャンセル・返金はお受けできません。<br>
                        求人の掲載停止はいつでも管理画面から行えます。
                    </td>
                </tr>
                <tr>
                    <th class="bg-light" style="vertical-align:middle;">中途解約</th>
                    <td>
                        掲載中の求人は管理画面から随時停止・削除が可能です。<br>
                        停止・削除後に発生した応募については成果報酬は発生しません。<br>
                        ただし、停止・削除前に発生済みの応募に係る成果報酬は発生します。
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="text-center mt-3">
        <a href="/" class="text-muted small"><i class="bi bi-arrow-left me-1"></i>トップページへ戻る</a>
    </div>

</div>
</div>
</div>
@endsection
