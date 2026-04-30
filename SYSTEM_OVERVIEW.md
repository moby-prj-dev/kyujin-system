# Care Entry（ケアエントリー）システム概要

沖縄県の介護・福祉職特化の求人サイト。掲載施設向けの成果報酬型求人サービス。

## 技術スタック

- **バックエンド**: Laravel（PHP）
- **フロントエンド**: Blade テンプレート / Bootstrap 5 / CDN
- **DB**: MySQL（Docker: sail ユーザー）
- **AI**: Gemini 2.5 Flash（コンテンツ自動生成）
- **スクレイピング**: Python + Playwright（ハローワーク求人取得）
- **インフラ**: Docker Compose / nginx / Ubuntu（本番: care-entry.net）
- **LINE**: LIFF / LINE Messaging API（応募・通知）

---

## 求人ソース

| source値 | 説明 |
|---|---|
| `care_entry` | 施設が直接掲載した求人（有料・成果報酬） |
| `hellowork` | ハローワークからスクレイピング＋AI生成LP |

---

## 主要ページ（公開側）

| URL | 概要 |
|---|---|
| `/` | トップ・検索フォーム（エリア/職種/雇用形態/条件で絞り込み、localStorage保存） |
| `/jobs/okinawa` | 求人一覧（SEO用・フィルター・ページネーション・noindex制御） |
| `/jobs/okinawa/{slug}` | エリア別求人一覧 |
| `/lp/{token}` | 個別求人LP（JobPosting構造化データ / OGP / 固定CTAバー） |
| `/lp/{token}/apply` | フォーム応募ページ |
| `/liff/{token}` | LINE応募ページ |
| `/articles` | SEO記事一覧 |
| `/articles/{slug}` | SEO記事詳細 |
| `/sitemap.xml` | 動的サイトマップ（全LP・記事・エリアページ含む） |
| `/company` | 運営者情報（沖縄デジタルワークス 代表者 岸本 安史） |
| `/legal` | 特定商取引法に基づく表記 |
| `/privacy-policy` | プライバシーポリシー |
| `/terms` | 利用規約 |

---

## 求人掲載フロー（Care Entry）

1. `/jobs/create` → 施設が求人フォームを入力
2. メール認証（`/jobs/verify/{token}`）
3. 掲載開始（無料トライアル3ヶ月 or 成果報酬）
4. 有効応募3件 or 3ヶ月経過でトライアル終了
5. 以降は成果報酬（有効応募1件あたり3,000円）

**二重登録防止**: メール or 電話番号が一致する公開中求人がある場合はブロック

---

## ハローワーク連携

### スクレイピング（Python）
- `scripts/hellowork_test.py`: Playwright で沖縄×介護・福祉求人を全ページ取得
- 出力: `scripts/hellowork_okinawa_kaigo.json`
- cron: 毎日3:00 AM に実行（`scripts/hellowork_cron.sh`）

### AI LP生成（Laravel）
- コマンド: `php artisan hellowork:generate-lps --limit=20`
- JSONを読み込み → 期限内のみ → 受付年月日降順 → 上位20件
- Gemini で4フィールドを生成:
  - `title`: h1用の自然なキャッチコピー（40文字以内）
  - `seo_title`: ブラウザタブ用SEOタイトル（パイプ形式、60文字以内）
  - `meta_description`: 説明文（120文字以内）
  - `description`: 仕事紹介文（400〜600文字）
- 対象外のHW求人はDBから自動削除
- cron: 毎週月曜 3:30 AM（UTC）

---

## SEO記事自動生成

- コマンド: `php artisan articles:generate`
- Gemini で介護・福祉関連のSEO記事を自動生成
- cron: 毎日 2:00 AM（UTC）

---

## 自動スケジュール（routes/console.php）

| 時刻（UTC） | コマンド | 内容 |
|---|---|---|
| 毎日 09:00 | `billing:send-trial-warnings` | トライアル終了7日前の警告メール |
| 毎日 09:10 | `billing:send-job-expired-notifications` | 掲載期限切れ通知メール |
| 毎月1日 08:00 | `billing:generate-monthly` | 前月分請求集計＆メール |
| 毎日 02:00 | `articles:generate` | SEO記事自動生成 |
| 毎週月曜 03:30 | `hellowork:generate-lps --limit=20` | HW求人LP生成 |
| 毎日 04:00 | `hellowork:cleanup-expired` | 期限切れHW求人削除 |

---

## 管理画面（`/admin`）

認証: セッションベース（`admin.auth` ミドルウェア）

| 機能 | URL |
|---|---|
| 求人一覧（Care Entry + HW混在） | `/admin/jobs` |
| 応募一覧・有効/無効判定 | `/admin/applications` |
| ハローワーク求人管理・編集 | `/admin/hellowork` |
| SEO記事管理・生成 | `/admin/articles` |
| 請求管理 | `/admin/billings` |
| 異議申立管理 | `/admin/disputes` |
| 設定 | `/admin/settings` |

### 求人一覧の操作
- 公開/非公開切替
- モニター設定（3ヶ月無料）
- 永久無料設定
- 管理者メモ
- 削除（forceDelete）
- 求人タイトルクリックでLP表示（新規タブ）

---

## DBの主要テーブル

| テーブル | 概要 |
|---|---|
| `job_listings` | 求人（source: care_entry / hellowork） |
| `applications` | 応募データ |
| `content_articles` | SEO記事 |
| `master_areas` | エリアマスタ（沖縄県各市区町村） |
| `master_job_types` | 職種マスタ |
| `master_employment_types` | 雇用形態マスタ |
| `master_conditions` | 勤務条件マスタ |
| `billing_summaries` | 請求集計 |

---

## SEO対策

- 動的サイトマップ（`/sitemap.xml`）
- robots.txt に Sitemap URL 記載
- JobPosting 構造化データ（Schema.org）— 全LP
- OGP / Twitter Card — トップ・LP
- noindex: Care Entry求人が0件のページ
- カスタム404ページ
- CSS: `public/css/` に外部化（welcome.css / jobs.css / lp.css）

---

## 環境変数（.env）主要項目

```
APP_TIMEZONE=UTC  # JSTにする場合は Asia/Tokyo
GEMINI_API_KEY=...
GEMINI_MODEL=gemini-2.5-flash
LINE_CHANNEL_ACCESS_TOKEN=...
LINE_CHANNEL_SECRET=...
```

---

## 運営者情報

- サービス名: Care Entry（ケアエントリー）
- 運営: 沖縄デジタルワークス 代表者 岸本 安史
- メール: careentry.info@gmail.com
- 電話: 070-6401-9492（平日10:00〜18:00）
- 所在地: 東京都台東区上野1丁目17番6号広小路ビル8F-B
