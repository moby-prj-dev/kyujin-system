# Care Entry（ケア・エントリー）引継ぎドキュメント

作成日：2026/04/20  
担当：moby0619

---

## 1. システム概要

沖縄特化・介護福祉分野の**成果報酬型求人プラットフォーム**。

- 掲載主がフォームで求人情報を登録すると、AIがタイトル・本文を自動生成しSEO最適化されたLPを公開
- 求職者はLPからLINEまたはフォームで応募できる
- 無料トライアル（掲載開始3か月 または 有効応募3件）終了後、有効応募1件につき3,000円の成果報酬が発生
- 掲載主はトークンURLで求人管理・応募確認ができる（ログイン不要）
- 運営者は管理画面（`/admin`）で企業・応募・請求を一元管理

---

## 2. 技術スタック

| 項目 | 内容 |
|---|---|
| フレームワーク | Laravel 11 |
| PHP | 8.4 |
| 開発環境 | Laravel Sail（Docker） |
| DB | MySQL 8.0 |
| フロントエンド | Bootstrap 5.3（CDN）+ Bootstrap Icons |
| AI文章生成 | Google Gemini API（`gemini-2.5-flash-lite`） |
| メール（ローカル） | Mailpit（http://localhost:8025） |
| メール（本番） | 未設定（Resend/SendGrid 推奨） |
| ファイルストレージ | Laravel Storage（public disk） |
| スパム対策 | reCAPTCHA v3（キー未設定） |

---

## 3. 環境構築

```bash
cp .env.example .env
# .env を編集（後述）

./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail artisan storage:link
```

---

## 4. .env 主要設定

```env
APP_NAME="Care Entry"
APP_URL=http://localhost          # 本番：https://care-entry.net

DB_CONNECTION=mysql
DB_HOST=mysql
DB_USERNAME=sail
DB_PASSWORD=password
FORWARD_DB_PORT=3307              # ローカルMySQL競合回避（DBeaver接続はポート3307）

# AI文章生成（Google Gemini）
GEMINI_API_KEY=AIzaSy...
GEMINI_MODEL=gemini-2.5-flash-lite

# reCAPTCHA v3（未設定）
RECAPTCHA_SITE_KEY=
RECAPTCHA_SECRET_KEY=

# 管理画面ログイン
ADMIN_ID=admin
ADMIN_PASSWORD=@password      # ← 必ず変更すること

# 課金同意文
BILLING_AGREEMENT_TEXT="応募が発生した場合、1件あたり3,000円の課金が発生します。"
BILLING_AGREEMENT_VERSION="v1.0"
```

---

## 5. ルート一覧

### 掲載主向け（求人管理）

| メソッド | URL | 説明 |
|---|---|---|
| GET | /jobs/create | 求人登録フォーム |
| POST | /jobs | 求人登録（throttle:5,60） |
| GET | /jobs/verify-sent | メール確認案内 |
| GET | /jobs/verify/{token} | メール認証リンク |
| GET | /jobs/duplicate | 重複登録案内 |
| GET | /jobs/resend | リンク再送フォーム |
| POST | /jobs/resend | リンク再送処理（throttle:5,60） |
| GET | /jobs/{token} | 求人管理ページ |
| PUT | /jobs/{token} | 求人更新 |
| PATCH | /jobs/{token}/close | 掲載停止 |
| PATCH | /jobs/{token}/reopen | 掲載再開 |
| DELETE | /jobs/{token} | 完全削除（論理削除） |

### 求職者向け（LP・応募）

| メソッド | URL | 説明 |
|---|---|---|
| GET | /lp/{token} | 求人LP |
| GET | /lp/{token}/apply | 応募フォーム |
| POST | /lp/{token}/apply | 応募登録（throttle:10,60） |
| GET | /lp/{token}/apply/thanks | 応募完了 |
| GET | /liff/{token} | LINE LIFF |

### 管理画面

| メソッド | URL | 説明 |
|---|---|---|
| GET | /admin/login | ログイン画面 |
| POST | /admin/login | ログイン処理 |
| GET | /admin/logout | ログアウト |
| GET | /admin/jobs | 企業・求人一覧 |
| GET | /admin/applications | 応募一覧 |
| PATCH | /admin/applications/{id} | 有効/無効切替 |
| GET | /admin/billings | 請求管理 |
| PATCH | /admin/billings/{id}/status | ステータス変更 |
| POST | /admin/billings/{id}/send | 請求メール再送 |
| POST | /admin/billings/generate | 月次集計実行 |

### 固定ページ

| URL | 説明 |
|---|---|
| /company | 運営者情報 |
| /privacy-policy | プライバシーポリシー |
| /terms | 利用規約 |
| /legal | 特定商取引法に基づく表記 |

---

## 6. 主要ファイル構成

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── JobController.php              # 求人登録・管理・更新・停止・削除
│   │   ├── JobVerificationController.php  # メール認証処理・SEO生成・公開
│   │   ├── JobResendController.php        # 確認・管理リンク再送
│   │   ├── LpController.php               # LP表示・閲覧ログ
│   │   ├── ApplyController.php            # フォーム応募
│   │   ├── LiffController.php             # LINE LIFF応募
│   │   └── Admin/
│   │       ├── AuthController.php         # 管理者ログイン
│   │       ├── JobController.php          # 企業・求人一覧
│   │       ├── ApplicationController.php  # 応募一覧・有効/無効切替
│   │       └── BillingController.php      # 請求管理・集計・送信
│   ├── Middleware/
│   │   └── AdminAuth.php                  # 管理画面認証
│   └── Requests/
│       └── StoreJobRequest.php            # 求人登録バリデーション・reCAPTCHA
├── Models/
│   ├── Job.php                            # 求人（SoftDeletes）
│   ├── Application.php                    # 応募（is_valid / is_billable）
│   ├── BillingSummary.php                 # 月次請求サマリー
│   └── ...（各マスター・ピボットモデル）
├── Services/
│   ├── SeoGeneratorService.php            # Gemini API でタイトル・本文生成
│   ├── ApplicationValidationService.php   # 有効応募判定
│   └── BillingService.php                 # 月次集計・請求メール送信
├── Mail/
│   ├── JobVerificationMail.php            # メール認証
│   ├── JobManageLinkMail.php              # 管理リンク再送
│   ├── TrialEndingWarningMail.php         # トライアル終了7日前警告
│   └── BillingSummaryMail.php             # 月次請求
└── Console/Commands/
    ├── SendTrialWarnings.php              # 警告メール送信コマンド
    └── GenerateMonthlyBillings.php        # 月次請求集計コマンド

resources/views/
├── layouts/app.blade.php                  # 共通レイアウト
├── welcome.blade.php                      # トップページ（LP形式）
├── jobs/
│   ├── create.blade.php                   # 求人登録フォーム
│   ├── manage.blade.php                   # 求人管理
│   ├── verify_sent.blade.php              # メール確認案内
│   ├── duplicate.blade.php                # 重複登録案内
│   └── resend.blade.php                   # リンク再送フォーム
├── lp/
│   ├── show.blade.php                     # 求人LP
│   ├── apply.blade.php                    # 応募フォーム
│   └── thanks.blade.php                   # 応募完了
├── admin/
│   ├── layouts/app.blade.php              # 管理画面レイアウト
│   ├── login.blade.php                    # ログイン
│   ├── jobs/index.blade.php               # 企業・求人一覧
│   ├── applications/index.blade.php       # 応募一覧
│   └── billings/index.blade.php           # 請求管理
├── emails/
│   ├── job_verification.blade.php         # メール認証メール
│   ├── job_manage_link.blade.php          # 管理リンクメール
│   ├── trial_ending_warning.blade.php     # トライアル終了警告メール
│   └── billing_summary.blade.php          # 請求メール
└── pages/
    ├── company.blade.php                  # 運営者情報
    ├── privacy_policy.blade.php           # プライバシーポリシー
    ├── terms.blade.php                    # 利用規約
    └── legal.blade.php                    # 特定商取引法

routes/
├── web.php                                # 全ルート定義
└── console.php                            # スケジューラー定義
```

---

## 7. データベース設計

### `job_listings`（求人）

| カラム | 型 | 説明 |
|---|---|---|
| id | bigint | PK |
| token | varchar(32) | 管理URL用トークン |
| status | enum | pending / draft / active / paused / closed |
| company_name | varchar | 会社名 |
| title | varchar | 求人タイトル |
| seo_title | varchar | SEOタイトル（AI生成） |
| meta_description | text | メタディスクリプション（AI生成） |
| description_generated | text | LP本文（AI生成） |
| free_text | text | 掲載主の自由記述 |
| photo_path | varchar | 写真パス |
| contact_email | varchar | 連絡先メール（企業識別キー） |
| contact_phone | varchar | 連絡先電話（ハイフンなし） |
| expires_at | timestamp | 掲載期限（登録日+3か月＝トライアル終了日） |
| email_verification_token | varchar(64) | メール認証トークン |
| email_verified_at | timestamp | メール認証完了日時 |
| trial_warning_sent_at | timestamp | 7日前警告メール送信日時 |
| deleted_at | timestamp | 論理削除 |

### `applications`（応募）

| カラム | 型 | 説明 |
|---|---|---|
| job_id | bigint | FK |
| applicant_name | varchar | 応募者名 |
| phone | varchar | 電話番号（元データ） |
| normalized_phone | varchar | 電話番号（正規化済） |
| email | varchar | メール（元データ） |
| normalized_email | varchar | メール（正規化済・小文字） |
| application_type | enum | line / form |
| status | enum | received / confirmed / closed |
| applied_at | timestamp | 応募日時 |
| is_valid | boolean | 有効応募フラグ |
| invalid_reason | varchar | 無効理由（後述） |
| is_billable | boolean | 請求対象フラグ |
| counted_at | timestamp | 有効応募確定日時 |

**invalid_reason の値：**

| 値 | 意味 |
|---|---|
| missing_required_fields | 必須項目不足 |
| duplicate_application | 重複応募 |
| spam_or_bot | スパム/ボット |
| test_submission | テスト投稿 |
| manually_invalidated | 管理者手動無効 |

### `billing_summaries`（月次請求サマリー）

| カラム | 型 | 説明 |
|---|---|---|
| contact_email | varchar | 企業識別（会社のメールアドレス） |
| billing_month | varchar(7) | 対象月（例：2026-04） |
| valid_count | int | 当月の有効応募件数 |
| billable_count | int | 当月の請求対象件数 |
| unit_price | int | 単価（3000固定） |
| total_amount | int | 合計金額 |
| status | enum | unbilled / sent / paid / on_hold |
| sent_at | timestamp | 請求メール送信日時 |

---

## 8. 課金・トライアルロジック

### 無料トライアルの条件

同一メールアドレスを持つ全求人票を「1社」として集計する。

```
以下のいずれか早い方で終了：
① 最初の求人票の expires_at（掲載開始から3か月）
② 会社全体の有効応募数が3件に到達
```

### 有効応募の判定フロー（ApplicationValidationService）

```
応募登録
  ↓
① 必須項目チェック（名前 + メールor電話）
  ↓
② メール・電話の正規化
  ↓
③ テスト投稿チェック（"テスト"/"test"/"あああ" 等）
  ↓
④ スパムチェック（"spam"/"bot@" 等）
  ↓
⑤ 同一求人内の重複チェック（normalized_email / normalized_phone）
  ↓
⑥ 有効/無効を確定 → is_valid・invalid_reason をセット
  ↓
⑦（有効の場合）トライアル終了判定 → is_billable をセット
```

### 請求単価

- 有効応募 1件 = **3,000円（税別）**

---

## 9. 自動メール一覧

| メール | タイミング | 送付先 | 仕組み |
|---|---|---|---|
| メール認証 | 求人登録時 | 掲載企業 | JobVerificationMail |
| 管理リンク再送 | 再送リクエスト時 | 掲載企業 | JobManageLinkMail |
| 応募通知 | 応募発生時（即時） | 掲載企業 | Mail::raw（ApplyController） |
| トライアル終了7日前警告 | 毎朝9時（cron） | 掲載企業 | TrialEndingWarningMail |
| 月次請求 | 毎月1日8時（cron） | 掲載企業 | BillingSummaryMail |

---

## 10. スケジューラー設定

`routes/console.php` に定義済み。本番サーバーで以下を `crontab -e` に追加する。

```
* * * * * cd /var/www/care-entry && php artisan schedule:run >> /dev/null 2>&1
```

確認コマンド：

```bash
php artisan schedule:list
php artisan billing:send-trial-warnings      # 手動実行
php artisan billing:generate-monthly         # 手動実行（前月）
php artisan billing:generate-monthly --month=2026-04  # 月指定
```

---

## 11. 管理画面（/admin）

### ログイン

- URL：`/admin/login`
- ID：`.env` の `ADMIN_ID`
- パスワード：`.env` の `ADMIN_PASSWORD`

### 画面一覧

| 画面 | URL | 主な機能 |
|---|---|---|
| 企業・求人一覧 | /admin/jobs | トライアル状態・有効/無効/請求対象件数・請求額を企業単位で表示 |
| 応募一覧 | /admin/applications | 応募データ・有効/無効の手動切替・絞り込み |
| 請求管理 | /admin/billings | 月次集計実行・ステータス管理・請求メール再送 |

### トライアル状態の表示

| 状態 | 意味 |
|---|---|
| 無料期間内 | トライアル継続中 |
| 終了まで7日以内 | 警告ゾーン |
| 無料期間終了 | 終了・請求対象なし |
| 請求対象あり | 課金発生中 |

---

## 12. 本番公開前チェックリスト

- [ ] `ADMIN_PASSWORD` を安全なパスワードに変更
- [ ] `APP_URL` を `https://care-entry.net` に変更
- [ ] SMTP設定（Resend / SendGrid 推奨）
- [ ] `RECAPTCHA_SITE_KEY` / `RECAPTCHA_SECRET_KEY` を設定
- [ ] サーバーに cron 追加（スケジューラー）
- [ ] `php artisan storage:link` 実行済み確認
- [ ] `php artisan config:cache` で設定キャッシュ

---

## 13. よくあるトラブルと対処

| 症状 | 原因 | 対処 |
|---|---|---|
| DBeaver から接続できない | ローカルMySQL(3306)との競合 | ポート3307で接続 |
| 画像が表示されない | storage:link 未実行 | `sail artisan storage:link` |
| LP が404になる | status が active でない / expires_at 超過 | 管理ページでステータス確認 |
| AIが文章を生成しない | Gemini APIキー / モデル名の誤り | `.env` 確認後 `artisan config:clear` |
| メールが届かない | SMTP未設定 / Mailpit未起動 | ローカルは http://localhost:8025 で確認 |
| 管理画面にログインできない | ADMIN_ID/PASSWORD の不一致 | `.env` を確認・`config:clear` を実行 |
| 月次集計が動かない | cron 未設定 | サーバーの crontab を確認 |

---

## 14. 未実装・今後の対応

### 優先度：高（本番稼働に必要）

- SMTP本番設定（Resend/SendGrid）
- reCAPTCHA v3 キー設定
- cron 設定
- 請求メールへの振込先口座情報追加

### 優先度：中

- 応募詳細（希望職種・メッセージ）を管理画面で表示
- 掲載期限切れ通知メール
- LINE LIFF の `LINE_LIFF_ID` 設定・動作確認

### 優先度：低

- 領収書・請求書 PDF 発行
- 未払い督促メール
- 管理画面での求人直接編集・削除
- 複数管理者アカウント対応

---

## 15. 運営者情報

| 項目 | 内容 |
|---|---|
| 事業者名 | ケアエントリー運営事務局 |
| 所在地 | 沖縄県豊見城市 |
| 電話番号 | 070-9401-9492 |
| メール | careentry.info@gmail.com |
| サービスURL | https://care-entry.net |
