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
                        本サービスは、月額料金と成果報酬を組み合わせた料金体系です。掲載主は以下の2種類のプランから選択します。
                        <table class="table table-bordered mt-2 mb-2" style="font-size:0.88rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>プラン</th>
                                    <th style="width:26%;">月額料金</th>
                                    <th style="width:34%;">成果報酬</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>ベーシック</strong></td>
                                    <td>0円</td>
                                    <td>有効応募1件につき<br><strong>3,000円(税別)</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>スタンダード</strong></td>
                                    <td><strong>5,000円(税別)/月</strong></td>
                                    <td>有効応募1件につき<br><strong>3,000円(税別)</strong></td>
                                </tr>
                            </tbody>
                        </table>
                        <span class="small">※ モニター企業様は、掲載開始から3か月間または有効応募3件までの成果報酬が免除されます(先に到達した条件で免除終了)。スタンダードプランの月額料金についても、モニター期間中は3か月間無料キャンペーンの対象となる場合があります。</span>
                    </td>
                </tr>
                <tr>
                    <th class="bg-light" style="vertical-align:middle;">有効応募の定義</th>
                    <td>
                        以下のすべての条件を満たす応募を「有効応募」とし、課金対象といたします。
                        <ol class="mb-2 mt-1">
                            <li>応募フォームの必須項目がすべて入力されていること</li>
                            <li>重複応募・スパム・なりすまし応募ではないこと</li>
                            <li>求職者の希望条件（職種カテゴリ・勤務地エリア等）と、求人情報の掲載条件が合致していること</li>
                        </ol>
                        <strong>課金タイミング：</strong>応募が成立した時点で課金が発生します。面接実施・採用の有無、および求職者との連絡継続性は、課金対象の判定には影響いたしません。<br>
                        <strong>合致判定：</strong>上記3点目の判定は当事業者（沖縄デジタルワークス）が行います。判定基準に関するご質問は <a href="mailto:careentry.info@gmail.com">careentry.info@gmail.com</a> までお問い合わせください。<br>
                        <span class="text-muted small">詳細は<a href="{{ route('terms') }}">利用規約 第7条</a>もご参照ください。</span>
                    </td>
                </tr>
                <tr>
                    <th class="bg-light" style="vertical-align:middle;">商品代金以外の<br>必要料金</th>
                    <td>なし（インターネット接続費用等はお客様のご負担となります）</td>
                </tr>
                <tr>
                    <th class="bg-light" style="vertical-align:middle;">支払時期</th>
                    <td>当事業者からの請求書発行後、指定の期日まで</td>
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
                        ただし、停止・削除前に発生済みの応募に係る成果報酬は発生します。<br>
                        <strong>スタンダードプランの月額料金について</strong>:プラン変更(スタンダード → ベーシック)または掲載停止を月中に行われた場合、その月の月額料金は日割り計算等の返金はいたしません。翌月分より新プラン料金または月額料金なし(掲載停止時)となります。
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
