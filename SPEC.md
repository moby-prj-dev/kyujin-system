# Care Entry — 求人システム 仕様書

> 最終更新: 2026-04-21

---

## 目次

1. [システム概要](#1-システム概要)
2. [技術スタック](#2-技術スタック)
3. [ディレクトリ構成](#3-ディレクトリ構成)
4. [データベース設計](#4-データベース設計)
5. [主要機能](#5-主要機能)
6. [ルーティング](#6-ルーティング)
7. [認証・認可](#7-認証認可)
8. [ビジネスロジック](#8-ビジネスロジック)
9. [メール通知](#9-メール通知)
10. [スケジュールタスク](#10-スケジュールタスク)
11. [外部連携](#11-外部連携)
12. [環境設定](#12-環境設定)
13. [開発・デプロイ手順](#13-開発デプロイ手順)

---

## 1. システム概要

**Care Entry** は沖縄の介護・福祉業界向けの成果報酬型求人掲載システム。

### ビジネスモデル

- **無料トライアル:** 掲載開始から 3ヶ月間、または有効応募 3件まで無料
- **課金開始後:** 有効応募 1件 = ¥3,000（月末集計、翌月請求）
- **掲載者（事業者）:** 求人票を作成・管理し、応募を受け取る
- **求職者:** 掲載されたLPから応募する（Webフォーム または LINE）

---

## 2. 技術スタック

| 区分 | 技術 |
|------|------|
| フレームワーク | Laravel 13 (PHP 8.3+) |
| DB（本番） | MySQL |
| DB（開発） | SQLite |
| フロントエンド | Blade + Bootstrap 5 + Tailwind CSS 4 |
| ビルドツール | Vite |
| メール送信 | Resend API |
| LINEメッセージング | LINE Bot SDK v12.5 |
| AI生成 | Google Gemini API (LP文章生成) |
| キュー/キャッシュ/セッション | データベースドライバ |
| 開発環境 | Laravel Sail (Docker) |

---

## 3. ディレクトリ構成

```
kyujin-system/
├── app/
│   ├── Console/Commands/       # バッチ処理（請求生成・警告メール）
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # 管理画面コントローラ
│   │   │   ├── JobController.php
│   │   │   ├── ApplyController.php
│   │   │   ├── LiffController.php
│   │   │   ├── LpController.php
│   │   │   └── LineWebhookController.php
│   │   ├── Middleware/
│   │   │   └── AdminAuth.php   # セッションベース管理者認証
│   │   └── Requests/
│   │       └── StoreJobRequest.php
│   ├── Mail/                   # メールクラス
│   ├── Models/                 # Eloquentモデル
│   └── Services/
│       ├── ApplicationValidationService.php  # 応募バリデーション・課金判定
│       ├── SeoGeneratorService.php           # LP文章生成
│       └── BillingService.php                # 請求集計
├── config/
│   ├── billing.php             # 課金設定（金額・条件・約款）
│   └── line.php                # LINE API設定
├── database/
│   ├── migrations/             # スキーマ定義（39件+）
│   └── seeders/
├── resources/views/
│   ├── admin/                  # 管理画面ビュー
│   ├── jobs/                   # 掲載者向けビュー
│   ├── lp/                     # LP・応募フォームビュー
│   └── emails/                 # メールテンプレート
└── routes/
    ├── web.php
    └── console.php             # スケジュールタスク定義
```

---

## 4. データベース設計

### コアテーブル

#### `job_listings` — 求人票

| カラム | 型 | 説明 |
|--------|-----|------|
| id | bigint PK | |
| token | varchar(32) | 掲載者アクセス用トークン（管理URL） |
| company_name | varchar | 会社名 |
| title | varchar | 求人タイトル |
| contact_email | varchar | 連絡先メール |
| contact_phone | varchar | 連絡先電話 |
| status | enum | active / paused / expired / deleted |
| email_verified_at | timestamp | メール認証完了日時 |
| expires_at | timestamp | トライアル終了日時（最初の認証から3ヶ月） |
| paused_at | timestamp | 掲載一時停止日時 |
| continued_at | timestamp | 継続申請日時 |
| salary_type | varchar | 給与形態 |
| salary_min | int | 最低給与 |
| salary_max | int | 最高給与 |
| photo_path | varchar | 求人写真パス |
| seo_title | varchar | SEOタイトル |
| meta_description | text | メタディスクリプション（160字） |
| description_generated | text | AI生成本文 |
| is_admin_hidden | boolean | 管理者による非表示フラグ |
| admin_memo | text | 管理者メモ |
| deleted_at | timestamp | ソフトデリート |

#### `applications` — 応募

| カラム | 型 | 説明 |
|--------|-----|------|
| id | bigint PK | |
| job_id | bigint FK | |
| application_type | enum | line / form |
| applicant_name | varchar | 応募者名 |
| phone | varchar | 電話番号（生値） |
| email | varchar | メールアドレス（生値） |
| normalized_phone | varchar | 正規化済み電話（数字のみ） |
| normalized_email | varchar | 正規化済みメール（小文字） |
| status | enum | pending / notified / failed |
| applied_at | timestamp | 応募日時 |
| is_valid | boolean | 有効応募フラグ |
| invalid_reason | varchar | 無効理由 |
| is_billable | boolean | 課金対象フラグ |
| billable_snapshot | json | 課金判定時のスナップショット（不変） |
| counted_at | timestamp | 集計日時 |

#### `billing_summaries` — 月次請求

| カラム | 型 | 説明 |
|--------|-----|------|
| id | bigint PK | |
| contact_email | varchar | 請求先メール |
| billing_month | varchar(7) | 請求月（Y-m形式） |
| valid_count | int | 有効応募数 |
| billable_count | int | 課金対象応募数 |
| unit_price | int | 単価（3,000円） |
| total_amount | int | 請求金額合計 |
| status | enum | unbilled / sent / paid / unpaid / on_hold / overdue |
| sent_at | timestamp | 請求メール送信日時 |

#### `billing_agreements` — 約款同意ログ

| カラム | 型 | 説明 |
|--------|-----|------|
| id | bigint PK | |
| job_id | bigint FK | |
| agreement_text | text | 同意した約款本文 |
| agreement_text_version | varchar | 約款バージョン（例: v1.1） |
| agreed_at | timestamp | 同意日時 |
| agreed_ip | varchar | 同意時IPアドレス |
| user_agent | varchar | 同意時ブラウザ情報 |

#### `audit_logs` — 操作ログ（追記専用）

| カラム | 型 | 説明 |
|--------|-----|------|
| id | bigint PK | |
| entity_type | varchar | 対象エンティティ種別 |
| entity_id | bigint | 対象エンティティID |
| action_type | varchar | 操作種別 |
| actor_type | varchar | 操作者種別（admin/system/employer） |
| actor_id | varchar | 操作者ID |
| action_payload | json | 操作詳細 |
| payload_hash | varchar | SHA-256ハッシュ（整合性検証） |

### ピボットテーブル

| テーブル | 関連 |
|---------|------|
| job_areas | job ↔ master_areas |
| job_job_types | job ↔ master_job_types |
| job_employment_types | job ↔ master_employment_types |
| job_conditions | job ↔ master_conditions |
| job_appeals | job ↔ master_appeals |

### マスターデータ

| テーブル | 内容 |
|---------|------|
| master_areas | エリアマスター（沖縄地域別） |
| master_job_types | 職種マスター |
| master_employment_types | 雇用形態マスター |
| master_conditions | 勤務条件マスター |
| master_appeals | アピールポイントマスター |

### 応募詳細テーブル

| テーブル | 内容 |
|---------|------|
| form_application_details | Webフォーム応募詳細 |
| line_application_details | LINE応募詳細（line_user_id, raw_answers_json等） |
| form_desired_job_types | フォーム応募の希望職種 |
| form_desired_conditions | フォーム応募の希望条件 |
| line_condition_answers | LINE応募の条件回答 |

### その他

| テーブル | 内容 |
|---------|------|
| email_verification_tokens | メール認証トークン |
| lp_views | LP閲覧数トラッキング（IP・UA記録） |

---

## 5. 主要機能

### 5-1. 求人票作成・管理（掲載者向け）

**求人票作成フロー:**
1. `/jobs/create` でフォーム入力
   - 会社名・求人タイトル
   - エリア（沖縄地域）
   - 職種・雇用形態・勤務条件・アピールポイント
   - 給与（形態・最低・最高）
   - 自由記述
   - 写真アップロード
   - 利用規約への同意（IP・UA・約款バージョン記録）
2. 重複チェック（同メール/電話で既存の有効求人があればエラー）
3. 未払い請求チェック（滞納があれば登録不可）
4. 認証メール送信
5. `/jobs/verify/{token}` でメール認証 → LP自動生成（Gemini API）・求人アクティベート

**求人管理ページ** (`/jobs/{token}`):
- 求人情報の編集
- 応募一覧の閲覧
- 月次請求サマリーの閲覧
- 掲載一時停止 / 再開
- 掲載継続申請（トライアル期間延長）
- 求人の完全削除

**ステータス遷移:**
```
active → paused（一時停止）→ active（再開）
active → expired（トライアル終了）
expired → active（継続申請後）
active/paused/expired → deleted（削除）
```

### 5-2. 応募フロー（求職者向け）

**2種類の応募方法:**

| 方法 | URL | 概要 |
|------|-----|------|
| Webフォーム | `/lp/{token}/apply` | 氏名・連絡先・希望条件入力 |
| LINE LIFF | `/liff/{token}` | LINE上のフォーム、LINE IDと紐付け |

**バリデーションパイプライン（ApplicationValidationService）:**
1. 必須項目チェック（氏名 + メールまたは電話番号）
2. テスト応募検出（"test", "テスト", "aaaa" 等のパターン）
3. スパム検出（spam, bot@, noreply, robot 等のキーワード）
4. 重複応募チェック（同求人に同メール/電話の応募が既にある場合）
5. 有効・課金フラグ自動判定

**応募後:**
- 有効応募なら掲載者にメール通知
- `is_valid`, `is_billable`, `billable_snapshot` を記録

### 5-3. 課金・トライアルロジック

**トライアル期間（無料枠）:**
- 初回メール認証から **3ヶ月間**（`expires_at`）
- または会社単位の有効応募が **3件**に達した時点
- どちらか早い方でトライアル終了

**課金判定（`isBillable`）:**
```
以下のいずれかを満たす場合 → 課金対象（is_billable = true）
  1. 現在日時 > expires_at（トライアル期間終了）
  2. 会社単位の累計有効応募 >= 3件
上記に該当しない場合 → 無料枠（is_billable = false）
```

**スナップショット保存:**
- 課金判定は応募受付時点で確定し `billable_snapshot` に保存
- 後から再計算しない（不変）

**月次請求集計:**
- 毎月1日 8:00 に `billing:generate-monthly` が実行
- `contact_email` ごとに有効・課金対象応募を集計
- 請求書メール（BillingSummaryMail）を送信
- `BillingSummary` レコードを作成/更新

### 5-4. LP（ランディングページ）表示

**URL:** `/lp/{token}`（掲載者の求人票トークンで識別）

**表示条件:**
- 求人ステータスが `active`
- `email_verified_at` が設定済み
- `is_admin_hidden = false`

**AI生成コンテンツ（Gemini API）:**
- キャッチコピー・サブタイトル
- 求人本文
- メタディスクリプション（160字）
- SEOタイトル（"職種名 | エリア"形式）

**表示要素:**
- エリア・職種・雇用形態・勤務条件・アピールポイント（タグ形式）
- 給与情報
- 求人写真
- 応募ボタン（フォーム or LINE LIFF）

**トラッキング:** アクセスごとにIPアドレス・UAを `lp_views` に記録

### 5-5. 管理画面

**URL:** `/admin`（`AdminAuth` ミドルウェアで保護）

**求人管理（`/admin/jobs`）:**
- 全求人一覧（会社名・応募数・有効数・ステータス等）
- フィルタ: テキスト検索 / ステータス / トライアル状況 / 継続状況
- `is_admin_hidden` トグル（非表示にすることでLPから除外）
- 管理者メモ追加（タイムスタンプ付き）

**応募管理（`/admin/applications`）:**
- 全応募一覧
- フィルタ: 有効/無効 / 課金対象 / メール / 求人
- 手動で `is_valid` を変更（有効 ↔ 無効）
- **注意:** 管理者が手動で有効にした応募は `is_billable = false`（課金対象外）

**請求管理（`/admin/billings`）:**
- 月次請求サマリー一覧
- フィルタ: 請求月 / ステータス
- ステータス更新（sent → paid / unpaid / on_hold / overdue）
- 請求メール再送信
- 手動請求生成ボタン

---

## 6. ルーティング

### パブリック

```
GET  /                              トップページ
GET  /lp/{token}                    求人LP
GET  /lp/{token}/apply              応募フォーム
POST /lp/{token}/apply              応募送信
GET  /lp/{token}/apply/thanks       応募完了ページ
GET  /liff/{token}                  LINE LIFF
POST /liff/{token}/apply            LINE応募送信
GET  /liff/{token}/thanks           LINE応募完了
POST /webhook/line                  LINE Webhookコールバック
GET  /company                       会社情報
GET  /privacy-policy                プライバシーポリシー
GET  /terms                         利用規約
GET  /legal                         特定商取引法表示
```

### 掲載者向け

```
GET  /jobs/create                   求人作成フォーム
POST /jobs                          求人登録
GET  /jobs/check-trial              トライアル状況確認（JSON）
GET  /jobs/verify-sent              認証メール送信完了ページ
GET  /jobs/verify/{token}           メール認証
GET  /jobs/duplicate                重複警告ページ
GET  /jobs/resend                   認証メール再送フォーム
POST /jobs/resend                   認証メール再送
GET  /jobs/{token}                  求人管理ページ
PUT  /jobs/{token}                  求人更新
PATCH /jobs/{token}/close           掲載一時停止
PATCH /jobs/{token}/reopen          掲載再開
POST /jobs/{token}/continue         掲載継続申請
DELETE /jobs/{token}                求人削除（ソフトデリート）
```

### 管理者向け（`admin.auth` ミドルウェア必須）

```
GET  /admin/login                   ログインフォーム
POST /admin/login                   ログイン
GET  /admin/logout                  ログアウト
GET  /admin/jobs                    求人一覧
PATCH /admin/jobs/{job}/toggle-hidden   非表示切り替え
PATCH /admin/jobs/{job}/memo            メモ保存
GET  /admin/applications            応募一覧
PATCH /admin/applications/{app}     有効/無効切り替え
GET  /admin/billings                請求一覧
PATCH /admin/billings/{billing}/status  ステータス更新
POST /admin/billings/{billing}/send     請求メール再送
POST /admin/billings/generate           手動請求生成
```

---

## 7. 認証・認可

### 管理者認証（セッションベース）

- 認証情報: `.env` の `ADMIN_ID` / `ADMIN_PASSWORD`
- セッションキー: `admin_authenticated`（boolean）
- ミドルウェア: `AdminAuth` → セッションを確認して未認証はリダイレクト

### 掲載者アクセス（トークンベース）

- 認証なし、32文字ランダムトークンで求人を識別
- `/jobs/{token}` にアクセスできれば管理権限あり
- セキュリティ: メール認証必須（認証前は非アクティブ）

### 求職者アクセス

- 認証なし（パブリックアクセス）
- 求人がアクティブ・認証済み・非非表示であることが条件

### CSRF

- 全体で有効
- 例外: `/webhook/line`（LINE側からの受信のため）

---

## 8. ビジネスロジック

### ApplicationValidationService

```
バリデーションフロー:
1. メール/電話番号の正規化
   - メール → 小文字変換
   - 電話 → 数字のみ抽出
2. 必須項目チェック（氏名 + メールまたは電話）
3. テスト応募検出
   - パターン: test, テスト, aaaa, 123456 等
4. スパム検出
   - キーワード: spam, bot@, noreply, robot 等
5. 重複応募チェック
   - 同求人 × 同正規化メール または 同正規化電話
6. is_valid = true を設定
7. isBillable() で is_billable を設定
8. billable_snapshot を JSON保存（不変）
```

### SeoGeneratorService

- Gemini API を使用してLP文章を自動生成
- `is_main_candidate` + `score` でメインアピールを選定
- 条件・職種・アピールからタグを生成
- メタディスクリプション（160字）を生成
- 自由記述が入力されている場合はそちらを優先

### BillingService

```
generateAndSendMonthly():
  1. 先月分の課金対象応募を contact_email 別に集計
  2. 有効数・課金対象数・合計金額を計算
  3. BillingSummary レコードを作成
  4. BillingSummaryMail を Resend 経由で送信
  5. status = 'sent', sent_at を記録
```

---

## 9. メール通知

| メールクラス | トリガー | 送信先 |
|-------------|---------|--------|
| `JobVerificationMail` | 求人作成時 | 掲載者 |
| `JobManageLinkMail` | 管理リンク再送要求 | 掲載者 |
| `TrialEndingWarningMail` | トライアル終了7日前（毎日9:00） | 掲載者 |
| `JobExpiredMail` | トライアル終了日超過（毎日9:10） | 掲載者 |
| `BillingSummaryMail` | 毎月1日8:00・手動実行 | 掲載者 |
| `JobContinuedMail` | 掲載継続申請クリック時 | 管理者 |
| 応募通知メール | 有効応募受付時 | 掲載者 |

---

## 10. スケジュールタスク

| 実行時刻 | コマンド | 処理内容 |
|---------|---------|---------|
| 毎日 09:00 | `billing:send-trial-warnings` | トライアル終了7日前の掲載者に警告メール |
| 毎日 09:10 | `billing:send-job-expired-notifications` | 期限切れ求人の掲載者に通知 |
| 毎月1日 08:00 | `billing:generate-monthly` | 前月分の請求集計・請求書メール送信 |

**本番サーバーのcron設定:**
```
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 11. 外部連携

### Resend（メール送信）

- 設定: `RESEND_API_KEY`
- 送信元: `noreply@care-entry.net`
- 用途: 全メール通知

### LINE

- **LINE Bot SDK v12.5**
- **LIFF（LINE Front-end Framework）:** 求職者の応募フォーム
- 設定: `LINE_CHANNEL_SECRET`, `LINE_CHANNEL_ACCESS_TOKEN`, `LINE_LIFF_ID`
- Webhook: `POST /webhook/line`（CSRF除外）

### Google Gemini API

- モデル: `gemini-2.5-flash-lite`（設定変更可能）
- 設定: `GEMINI_API_KEY`, `GEMINI_MODEL`
- 用途: 求人LP本文・キャッチコピー・メタ情報の自動生成

---

## 12. 環境設定

### 主要 .env 変数

```env
APP_NAME=Laravel
APP_ENV=local             # 本番: production
APP_URL=http://localhost  # 本番: https://care-entry.net
APP_DEBUG=true            # 本番: false

# DB（開発）
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# DB（本番）
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kyujin
DB_USERNAME=sail          # 本番は適切なユーザー名
DB_PASSWORD=

# セッション・キュー・キャッシュ
SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

# メール
MAIL_MAILER=resend        # 開発: log
MAIL_FROM_ADDRESS=noreply@care-entry.net
RESEND_API_KEY=re_xxxx

# LINE
LINE_CHANNEL_SECRET=
LINE_CHANNEL_ACCESS_TOKEN=
LINE_LIFF_ID=

# Gemini
GEMINI_API_KEY=
GEMINI_MODEL=gemini-2.5-flash-lite

# 管理画面
ADMIN_ID=admin
ADMIN_PASSWORD=           # 本番は強力なパスワードを設定
ADMIN_EMAIL=careentry.info@gmail.com
```

### 課金設定（`config/billing.php`）

```php
'amount'                => 3000,           // 課金単価（円）
'agreement_version'     => 'v1.1',         // 約款バージョン
'continue_warning_days' => 7,              // 警告メールの日数
'admin_email'           => env('ADMIN_EMAIL')
```

---

## 13. 開発・デプロイ手順

### 開発環境セットアップ

```bash
# リポジトリクローン後
composer install
npm install --ignore-scripts

# Sail（Docker）起動
./vendor/bin/sail up -d

# DB初期化
./vendor/bin/sail artisan migrate --seed

# ストレージリンク作成
./vendor/bin/sail artisan storage:link

# フロントエンドビルド（開発）
npm run dev
```

### 本番デプロイ

```bash
composer install --no-dev --optimize-autoloader
npm run build

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
php artisan migrate --force

# cron設定
* * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1
```

---

## データ整合性・コンプライアンス

| 機能 | 詳細 |
|------|------|
| ソフトデリート | `job_listings` は `deleted_at` で論理削除 |
| 操作ログ | `audit_logs` に全主要操作を記録（追記専用・ハッシュ検証） |
| メール認証 | 求人アクティベート前に必須 |
| 約款同意記録 | バージョン・IP・UA・日時を保存 |
| 課金スナップショット | 応募時点で確定、後から変更不可 |
| ハッシュ検証 | `audit_logs.payload_hash` は SHA-256 で整合性検証 |
