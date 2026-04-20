# Care Entry（ケア・エントリー）引継ぎ資料

> 作成日：2026-04-21

---

## 1. サービス概要

**Care Entry（ケア・エントリー）** は、沖縄県の介護・福祉事業者向け成果報酬型求人掲載サービスです。

- 求人掲載主（事業者）が求人を登録し、求職者が LINE または Web フォームから応募する
- 掲載開始から **3か月間** または **有効応募3件** までは無料
- 無料期間終了後は **有効応募1件につき3,000円（税別）** が発生
- 月次で請求書メールを送付し、振込入金を確認して管理画面でステータス更新

---

## 2. 技術スタック

| 項目 | 内容 |
|------|------|
| フレームワーク | Laravel 11 / PHP 8.4 |
| DB | MySQL（Docker: Sail） |
| メール送信 | Resend |
| LINE連携 | LINE Messaging API SDK |
| AI生成 | Google Gemini API（求人LP文章自動生成） |
| ストレージ | ローカル public disk（求人写真） |
| キュー | Database |
| キャッシュ | Database |
| 管理画面認証 | セッションベース（ADMIN_ID / ADMIN_PASSWORD） |
| フロントエンド | Bootstrap 5 |
| 開発環境 | Docker（Laravel Sail） |

---

## 3. 環境構築手順

### 開発環境（ローカル）

```bash
# 1. リポジトリクローン後
cp .env.example .env  # または既存の .env を配置

# 2. パッケージインストール
./vendor/bin/sail composer install

# 3. Sail 起動
./vendor/bin/sail up -d

# 4. マイグレーション＆シーダー
./vendor/bin/sail artisan migrate --seed

# 5. ストレージリンク
./vendor/bin/sail artisan storage:link
```

### 本番環境（care-entry.net）

```bash
# デプロイ後
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

---

## 4. 環境変数（.env）

### 必須設定項目

| キー | 説明 |
|------|------|
| `APP_KEY` | Laravelアプリケーションキー（`artisan key:generate`） |
| `APP_URL` | 本番: `https://care-entry.net` / ローカル: `http://localhost` |
| `DB_HOST / DB_DATABASE / DB_USERNAME / DB_PASSWORD` | DB接続情報 |
| `RESEND_API_KEY` | Resend APIキー（メール送信） |
| `MAIL_FROM_ADDRESS` | 送信元メール `noreply@care-entry.net` |
| `LINE_CHANNEL_SECRET` | LINE Messaging API チャンネルシークレット |
| `LINE_CHANNEL_ACCESS_TOKEN` | LINE Messaging API アクセストークン |
| `LINE_LIFF_ID` | LINE LIFF ID |
| `GEMINI_API_KEY` | Google Gemini API キー（LP文章生成） |
| `GEMINI_MODEL` | 使用するGeminiモデル（現在: `gemini-2.5-flash-lite`） |
| `ADMIN_ID` | 管理画面ログインID |
| `ADMIN_PASSWORD` | 管理画面ログインパスワード（本番前に必ず変更） |

---

## 5. ルート一覧

### 求人掲載主向け

| メソッド | URL | 説明 |
|----------|-----|------|
| GET | `/jobs/create` | 求人作成フォーム |
| POST | `/jobs` | 求人作成・保存 |
| GET | `/jobs/verify-sent` | メール確認待ち画面 |
| GET | `/jobs/verify/{token}` | メールアドレス確認 |
| GET | `/jobs/duplicate` | 重複求人警告画面 |
| GET | `/jobs/resend` | 確認メール再送フォーム |
| POST | `/jobs/resend` | 確認メール再送実行 |
| GET | `/jobs/{token}` | 求人管理ページ |
| PUT | `/jobs/{token}` | 求人情報更新 |
| PATCH | `/jobs/{token}/close` | 掲載停止 |
| PATCH | `/jobs/{token}/reopen` | 掲載再開 |
| DELETE | `/jobs/{token}` | 求人完全削除 |
| GET | `/jobs/check-trial` | トライアル終了判定（JSON） |

### 求職者向け

| メソッド | URL | 説明 |
|----------|-----|------|
| GET | `/lp/{token}` | 求人LP |
| GET | `/lp/{token}/apply` | フォーム応募画面 |
| POST | `/lp/{token}/apply` | フォーム応募送信 |
| GET | `/lp/{token}/apply/thanks` | 応募完了画面 |
| GET | `/liff/{token}` | LINE LIFF画面 |
| POST | `/liff/{token}/apply` | LINE応募送信 |
| GET | `/liff/{token}/thanks` | LINE応募完了画面 |
| POST | `/webhook/line` | LINE Webhook |

### 管理画面

| メソッド | URL | 説明 |
|----------|-----|------|
| GET | `/admin/login` | ログイン |
| GET | `/admin/jobs` | 企業・求人一覧 |
| GET | `/admin/applications` | 応募一覧 |
| PATCH | `/admin/applications/{id}` | 応募の有効/無効切り替え |
| GET | `/admin/billings` | 請求管理 |
| POST | `/admin/billings/generate` | 月次請求生成・メール送信 |
| PATCH | `/admin/billings/{id}/status` | 請求ステータス更新 |
| POST | `/admin/billings/{id}/send` | 請求メール再送 |

---

## 6. 主要ビジネスロジック

### 6-1. 求人掲載フロー

```
① 求人作成フォーム入力
② メールアドレス確認メール送信
③ 確認リンクをクリック → ステータス: active
④ Gemini API で LP タイトル・本文自動生成
⑤ LP が公開される（/lp/{token}）
```

### 6-2. 応募フロー

```
① 求職者が LP から応募（フォームまたは LINE LIFF）
② ApplicationValidationService で自動判定
   - 必須項目チェック
   - テスト投稿フィルタ（名前で判定）
   - スパム/ボット判定
   - 重複応募チェック（同一求人内のメールor電話）
③ is_valid = true の場合、課金対象判定
④ 掲載主へ応募通知メール送信
```

### 6-3. 課金判定ロジック

| 条件 | is_billable |
|------|------------|
| 応募日時がトライアル期間内 かつ 有効応募累計が3件未満 | false（無料） |
| 応募日時がトライアル終了後 | true（課金） |
| 有効応募累計が3件以上（当該応募除く） | true（課金） |
| is_valid = false | false（課金なし） |
| 管理者が手動で有効化した応募 | false（課金なし） |

> **重要**: `is_billable` は応募受付時点で確定。後から再計算しない。
> `billable_snapshot` カラムに受付時の値を保持している。

### 6-4. トライアル終了判定

以下のいずれかで「トライアル終了」と判定（`JobController::isTrialEnded()`）：

1. 最初の求人の `expires_at` を過ぎている（掲載開始から3か月）
2. 会社全体の有効応募累計が3件以上

### 6-5. 月次請求フロー

```
① 毎月1日8:00 に billing:generate-monthly が自動実行
② 前月の有効応募を集計 → BillingSummary レコード作成
③ 課金対象がある場合、請求メールを自動送信
④ 管理画面で入金確認後、ステータスを「入金済」に手動更新
```

**振込先口座**

| 項目 | 内容 |
|------|------|
| 銀行 | 住信SBIネット銀行 |
| 支店 | バナナ支店 |
| 口座番号 | 9466315 |
| 名義 | キシモト　ヤスシ |

---

## 7. Cronスケジュール

本番サーバーで以下の cron を設定すること：

```cron
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

| コマンド | 実行タイミング | 内容 |
|----------|--------------|------|
| `billing:send-trial-warnings` | 毎日 9:00 | トライアル終了7日前の警告メール送信 |
| `billing:send-job-expired-notifications` | 毎日 9:10 | 掲載期限切れ通知メール送信 |
| `billing:generate-monthly` | 毎月1日 8:00 | 前月分請求集計・メール送信 |

---

## 8. 送信メール一覧

| メールクラス | タイミング | 宛先 | 件名 |
|-------------|-----------|------|------|
| JobVerificationMail | 求人作成時 | 掲載主 | メールアドレスの確認をお願いします |
| JobManageLinkMail | 管理リンク再送時 | 掲載主 | 編集用リンクをお送りします |
| TrialEndingWarningMail | トライアル終了7日前 | 掲載主 | 無料トライアル期間終了のお知らせ |
| JobExpiredMail | 掲載期限切れ時 | 掲載主 | 求人掲載期間が終了しました |
| BillingSummaryMail | 月次請求時 | 掲載主 | YYYY-MM ご請求書のご案内 |
| （応募通知） | 応募受信時 | 掲載主 | 新しい応募が届きました |

---

## 9. 管理画面の使い方

URL: `https://care-entry.net/admin/login`

### 企業・求人一覧（/admin/jobs）
- 会社ごとの求人状況・トライアル状態を一覧確認
- トライアル状態: `active`（無料期間中）/ `billing`（課金中）/ `ending_soon`（7日以内終了）/ `ended`（終了）

### 応募一覧（/admin/applications）
- 全応募を確認（有効・無効・課金対象フィルター対応）
- 誤判定された応募を手動で有効/無効化可能
- **手動有効化した応募は課金対象にならない**（仕様）

### 請求管理（/admin/billings）
- 月次請求の一覧・ステータス管理
- ステータス遷移: `未請求` → `送付済` → `入金済` / `未払い` / `期限超過` / `保留`
- 入金確認後は手動でステータスを「入金済」に変更する
- **期限超過** になると対象企業は新規求人登録・再掲載が不可になる

---

## 10. データベース主要テーブル

| テーブル | 説明 | 主要カラム |
|---------|------|-----------|
| `job_listings` | 求人 | status, token, contact_email, expires_at, email_verified_at |
| `applications` | 応募 | is_valid, invalid_reason, is_billable, billable_snapshot, applied_at |
| `billing_summaries` | 月次請求集計 | contact_email, billing_month, valid_count, billable_count, total_amount, status |
| `billing_agreements` | 利用規約同意ログ | job_id, agreement_text_version, agreed_at, agreed_ip |
| `audit_logs` | 全操作の監査ログ（append-only） | entity_type, entity_id, action_type, action_payload |
| `master_areas` | 地域マスター | prefecture, region, name, is_active, sort_order |
| `master_job_types` | 職種マスター | category, name, is_active, score, is_main_candidate |
| `master_conditions` | 勤務条件マスター | category, name, is_active, score, is_main_candidate |
| `master_appeals` | アピールマスター | category, name, is_active, score, is_main_candidate |

---

## 11. 本番公開前チェックリスト

- [ ] `ADMIN_PASSWORD` を安全なパスワードに変更
- [ ] `APP_ENV=production` / `APP_DEBUG=false` に変更
- [ ] `APP_URL=https://care-entry.net` に設定
- [ ] `RESEND_API_KEY` を本番用キーに確認
- [ ] `LINE_LIFF_ID` を本番用に設定
- [ ] cron 設定（`* * * * * php artisan schedule:run`）
- [ ] `php artisan config:cache && route:cache && view:cache`
- [ ] `php artisan storage:link`
- [ ] SSL 証明書の確認

---

## 12. よくあるトラブルと対処

| 症状 | 原因 | 対処 |
|------|------|------|
| メールが届かない | Resend APIキー or 送信元ドメイン未設定 | `.env` の `RESEND_API_KEY` と Resend ダッシュボードのドメイン確認 |
| LP が表示されない | 求人ステータスが active 以外 | 管理画面で求人ステータスを確認 |
| 請求が二重に生成される | generate-monthly を手動で複数回実行 | `billing_summaries` テーブルの重複レコードを確認・削除 |
| 課金件数がおかしい | is_billable の値を確認 | `applications` テーブルの `is_billable`、`billable_snapshot` を確認 |
| LP文章が生成されない | Gemini API エラー | `GEMINI_API_KEY` 確認、`storage/logs` のログ確認 |
| 管理画面にログインできない | ADMIN_ID / ADMIN_PASSWORD の不一致 | `.env` の値を確認 |

---

## 13. 連絡先・アカウント情報

| サービス | 情報 |
|---------|------|
| サービス問い合わせメール | careentry.info@gmail.com |
| Resend（メール送信） | アカウント登録メールアドレスで管理 |
| Mailgun（旧メール、現在未使用） | mg.care-entry.net ドメイン登録済み |
| LINE Developers | チャンネル: Care Entry |
| Google Cloud（Gemini API） | APIキーで管理 |
| 振込口座名義 | キシモト　ヤスシ |
