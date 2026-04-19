# 求人掲載システム 引継ぎ資料

作成日：2026/04/19  
担当：moby0619

---

## 1. システム概要

沖縄特化の求人LP自動生成・LINE応募・応募時課金システム。

- 掲載主がフォームで求人情報を登録すると、SEO最適化されたLPが自動生成される
- 求職者はLPからLINEまたはフォームで応募できる
- 応募1件ごとに課金が発生する（応募時課金モデル）
- 掲載主はトークンURLで求人管理・応募者確認ができる

---

## 2. 技術スタック

| 項目 | 内容 |
|---|---|
| フレームワーク | Laravel 11 |
| 開発環境 | Laravel Sail（Docker） |
| DB | MySQL 8.0 |
| フロントエンド | Bootstrap 5.3（CDN） |
| ファイルストレージ | Laravel Storage（public disk） |
| メール | SMTP（未設定） |
| LINE連携 | 未実装 |

---

## 3. 環境構築

```bash
# リポジトリクローン後
cp .env.example .env
# .envを編集（DB・メール等）

./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail artisan storage:link
```

### .env 主要設定

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password
FORWARD_DB_PORT=3307   # ローカルMySQL競合回避用

BILLING_AGREEMENT_TEXT="応募が発生した場合、1件あたり〇〇円の課金が発生します。"
BILLING_AGREEMENT_VERSION="v1.0"
```

> **注意**：WSL2環境ではローカルMySQLとDockerのポートが競合するため `FORWARD_DB_PORT=3307` を設定。DBeaver等から接続する場合はポート3307を使用。

---

## 4. データベース設計

### 主要テーブル

#### `job_listings`（求人）

| カラム | 型 | 説明 |
|---|---|---|
| id | bigint | PK |
| token | varchar(32) | 管理URL用トークン（掲載主に渡す） |
| status | enum | draft / active / paused / closed |
| title | varchar | 求人タイトル（SEO生成） |
| seo_title | varchar | SEOタイトル |
| meta_description | text | メタディスクリプション |
| description_generated | text | LP本文（自動生成） |
| free_text | text | 掲載主による自由記述 |
| photo_path | varchar | 写真パス |
| contact_email | varchar | 連絡先メール |
| contact_phone | varchar | 連絡先電話（ハイフンなし10〜11桁） |
| expires_at | timestamp | 無料掲載期限（登録日+3ヶ月、停止期間除外） |
| paused_at | timestamp | 掲載停止開始日時（再掲載時クリア） |
| deleted_at | timestamp | 論理削除日時（SoftDeletes） |
| created_at / updated_at | timestamp | 自動 |

#### マスターテーブル（全て `sort_order` / `is_active` カラムあり）

| テーブル | 内容 |
|---|---|
| `master_areas` | エリア（沖縄のみ、region・prefecture・name） |
| `master_job_types` | 職種（category・name） |
| `master_employment_types` | 雇用形態（name） |
| `master_conditions` | 勤務条件（category・name） |
| `master_appeals` | アピールポイント（category・name） |

#### ピボットテーブル（求人と各マスターの多対多）

| テーブル | 説明 |
|---|---|
| `job_areas` | job_id / area_id |
| `job_job_types` | job_id / job_type_id |
| `job_employment_types` | job_id / employment_type_id |
| `job_conditions` | job_id / condition_id |
| `job_appeals` | job_id / appeal_id |

#### `applications`（応募）

| カラム | 型 | 説明 |
|---|---|---|
| job_id | bigint | FK |
| applicant_name | varchar | 応募者氏名 |
| phone | varchar | 電話番号 |
| email | varchar | メール |
| application_type | enum | line / form |
| status | enum | received / confirmed / closed |
| applied_at | timestamp | 応募日時 |

#### その他テーブル

| テーブル | 説明 |
|---|---|
| `billing_agreements` | 応募時課金への同意記録 |
| `billings` | 課金レコード |
| `lp_views` | LP閲覧ログ（job_id / ip_address / user_agent / viewed_at） |
| `audit_logs` | 操作ログ |
| `email_verification_tokens` | 編集URL再発行用トークン（未実装） |
| `line_entry_tokens` | LINE応募用トークン（未実装） |

---

## 5. ルート一覧

| メソッド | URL | 名前 | 説明 |
|---|---|---|---|
| GET | /jobs/create | jobs.create | 求人登録フォーム |
| POST | /jobs | jobs.store | 求人登録処理 |
| GET | /jobs/{token} | jobs.manage | 求人管理ページ |
| PUT | /jobs/{token} | jobs.update | 求人更新処理 |
| PATCH | /jobs/{token}/close | jobs.close | 掲載停止 |
| PATCH | /jobs/{token}/reopen | jobs.reopen | 掲載再開 |
| DELETE | /jobs/{token} | jobs.destroy | 完全削除（論理削除） |
| GET | /lp/{token} | lp.show | 求人LP表示（求職者向け） |

---

## 6. 主要ファイル構成

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── JobController.php      # 求人登録・管理・更新・停止・削除
│   │   └── LpController.php       # LP表示・閲覧ログ記録
│   └── Requests/
│       └── StoreJobRequest.php    # 求人登録バリデーション
├── Models/
│   ├── Job.php                    # SoftDeletes・expires_at/paused_at cast
│   ├── JobArea.php
│   ├── JobJobType.php
│   ├── JobEmploymentType.php
│   ├── JobCondition.php
│   ├── JobAppeal.php
│   ├── MasterArea.php
│   ├── MasterJobType.php
│   ├── MasterEmploymentType.php
│   ├── MasterCondition.php
│   ├── MasterAppeal.php
│   ├── Application.php
│   ├── BillingAgreement.php
│   ├── AuditLog.php
│   └── LpView.php
└── Services/
    └── SeoGeneratorService.php    # SEOテキスト自動生成

resources/views/
├── layouts/app.blade.php          # 共通レイアウト
├── jobs/
│   ├── create.blade.php           # 求人登録フォーム
│   └── manage.blade.php           # 求人管理ページ
└── lp/
    └── show.blade.php             # 求人LP

config/
└── billing.php                    # 課金同意文・バージョン管理
```

---

## 7. 実装済み機能

### 求人登録（`/jobs/create`）
- エリア・職種・雇用形態・勤務条件・アピールポイントの複数選択（タブUI）
- 連絡先メール・電話番号（ハイフンなし10〜11桁）
- 自由記述テキストエリア・写真アップロード（1枚・5MB以内）
- 応募時課金への同意チェック
- localStorage によるフォーム入力値の保持（リロード後も復元）
- 登録後にSEOテキスト（タイトル・メタ・本文）を自動生成
- `expires_at = 登録日 + 3ヶ月` をセット
- 課金同意・操作ログを自動記録

### 求人管理（`/jobs/{token}`）
- 掲載ステータスバッジ表示
- 掲載開始日・無料掲載期限表示（残り14日以内は黄色、期限切れは赤色）
- 公開中の場合はLP URLコピーボタン表示
- 応募件数・応募者一覧（ページネーション付き）
- 求人情報編集フォーム（タブUI・写真更新対応）
- **掲載停止**：LP が404になり、`paused_at` を記録
- **掲載再開**：停止期間分 `expires_at` を自動延長
- **完全削除**：論理削除（確認ダイアログ付き、復元不可）

### 求人LP（`/lp/{token}`）
- status が active かつ `expires_at` 未到達の場合のみ表示
- エリア・職種・雇用形態・勤務条件・アピールポイントを表示
- 写真・自由記述・SEO生成本文を表示
- LINE応募ボタン・フォーム応募ボタン（スティッキー表示）
- 閲覧ログを `lp_views` に記録

### 無料掲載期間ロジック
```
登録時：expires_at = created_at + 3ヶ月
掲載停止時：paused_at = 停止日時
掲載再開時：expires_at += (now - paused_at の秒数)、paused_at = null
LP表示時：expires_at が過去なら 404
```

---

## 8. 設計上の決定事項（経緯付き）

| 項目 | 決定内容 | 理由 |
|---|---|---|
| 管理URLの認証 | トークンURLのみ（認証なし） | シンプルに運用するため |
| 電話番号フォーマット | ハイフンなし10〜11桁強制 | 企業の重複排除キーとして使用するため |
| 求人の地域 | 沖縄のみ | 初期フェーズのスコープ |
| 掲載停止の扱い | status=closed、管理ページは存続 | データ・応募履歴を保持するため |
| 削除方式 | 論理削除（SoftDeletes） | 誤削除時の調査・復元のため |
| 無料掲載期間 | 3ヶ月（停止期間除外） | 停止期間を掲載期間に含めない公平性のため |
| SEO生成タイミング | 登録時・更新時に同期生成 | 登録直後にLPが表示できる状態を保つため |

---

## 9. 未実装（今後の対応）

### フェーズ1：応募フォーム（優先度：高）
- `GET /lp/{token}/apply` — 応募フォームページ
- `POST /lp/{token}/apply` — 応募登録処理
- `applications` テーブルへの保存
- 掲載主へのメール通知

### フェーズ2：編集URL再発行（優先度：中）
- メールアドレスまたは電話番号で本人確認
- `email_verification_tokens` テーブルを使った有効期限付き再発行URL
- 再発行メール送信

### フェーズ3：LINE Bot Webhook + LIFF（優先度：中）
- LINE Developers でチャネル作成・Webhook URL登録が必要
- `POST /webhook/line` — メッセージ受信・応募登録処理
- LIFF アプリ（`/liff/{token}`）— LINE ログインで応募者情報取得
- `line_application_details` / `line_entry_tokens` テーブルを活用

### フェーズ4：仕様書最終版（優先度：低）
- 全機能完成後に本資料をベースに最終仕様書を作成

---

## 10. よくあるトラブルと対処

| 症状 | 原因 | 対処 |
|---|---|---|
| DBeaver から接続できない | ローカルMySQL(3306)との競合 | ポート3307で接続 |
| `storage/` の画像が表示されない | `storage:link` 未実行 | `sail artisan storage:link` を実行 |
| LP が404になる | status が active でない、または expires_at 超過 | 管理ページでステータスと有効期限を確認 |
| 無料掲載期限が「—」表示 | expires_at カラム追加前のデータ | tinker で `expires_at = created_at + 3ヶ月` を手動設定 |
| フォームのチェックが復元されない | localStorage が無効な環境 | プライベートブラウザでは動作しない（仕様） |
