# ケアエントリー タスク実装状況（引き継ぎ用）

引き継ぎ文書（次タスク引き継ぎ）と照らし合わせた実装済み・未着手の整理。

---

## 施設向け管理ページ（`/jobs/{token}`）

| 項目 | 状態 | 備考 |
|---|---|---|
| 自施設の応募一覧 | ✅ 実装済み | ページネーション付き |
| 有効応募カウンター（無料枠残り表示） | ✅ 実装済み | 「無料枠残りN件」表示あり |
| トライアル残日数表示 | ✅ 実装済み | 「残りN日」表示あり |
| 掲載期限・更新導線 | ✅ 実装済み | 継続ボタンあり |

→ **施設向け管理ページは大部分実装済み。新規開発不要。**

---

## 管理画面（`/admin`）

| 項目 | 状態 | 備考 |
|---|---|---|
| `/admin/applications` 有効/無効判定UI | ✅ 実装済み | |
| `/admin/disputes` 異議申立フロー | ✅ 実装済み | 承認・却下ボタンあり |
| 求人一覧（削除・公開切替・メモ） | ✅ 実装済み | |
| HW求人管理・編集 | ✅ 実装済み | `/admin/hellowork` |
| 請求管理 | ✅ 実装済み | `/admin/billings` |

---

## billing周り

| 項目 | 状態 | 備考 |
|---|---|---|
| `billing:generate-monthly` コマンド | ✅ 実装済み | 毎月1日 08:00 UTC |
| `billing:send-trial-warnings` コマンド | ✅ 実装済み | 毎日 09:00 UTC |
| `billing:send-job-expired-notifications` | ✅ 実装済み | 毎日 09:10 UTC |
| **実機テスト（ダミーデータ投入）** | ❌ 未実施 | **要対応：初課金月トラブル防止** |
| トライアル終了警告メールの文面確認 | ❌ 未確認 | モニター向けトーンか要チェック |

---

## SEO・コンテンツ

| 項目 | 状態 | 備考 |
|---|---|---|
| JobPosting構造化データ | ✅ 実装済み | 全LPに適用 |
| サイトマップ（動的生成） | ✅ 実装済み | LP・記事・エリアページ含む |
| robots.txt Sitemap URL | ✅ 実装済み | |
| OGP / Twitter Card | ✅ 実装済み | トップ・LP |
| カスタム404ページ | ✅ 実装済み | |
| SEO記事自動生成 | ⚠️ 要調査 | コマンド自体は動作するがcronで実行されていない疑い |
| HW求人LP自動生成（20件/週） | ✅ 実装済み | 毎週月曜 03:30 UTC |

---

## 法的ページ

| 項目 | 状態 | 備考 |
|---|---|---|
| 運営者情報 | ✅ 更新済み | 沖縄デジタルワークス 代表者 岸本 安史 |
| 特定商取引法 | ✅ 更新済み | 住所・電話番号修正済み |
| **「有効応募の定義」の明記** | ✅ 完了 | 特商法・利用規約第7条に明記済み |

---

## 未着手・要対応まとめ

優先度順：

1. **⚠️ articles:generate がcronで動いていない** — 手動実行は成功するがバッチ未実行。`storage/logs/laravel.log` で `articles` のログを確認。Gemini APIキーがcron実行時に読めていない可能性あり
2. **billing実機テスト** — ダミー応募データ投入 → `billing:generate-monthly` 動作確認 → メール文面・金額確認
3. **トライアル終了警告メールの文面確認** — `resources/views/emails/trial_ending_warning.blade.php`
4. **HW求人LP品質チェック** — Gemini生成タイトル・本文の目視確認・プロンプト調整

### 完了済み
- ✅ 有効応募の定義を特商法・利用規約に明記
- ✅ モニター募集LPは `/client` ページが既存で対応済み（新規作成不要）

---

## 参考ファイルパス

| 内容 | パス |
|---|---|
| 技術仕様全体 | `SYSTEM_OVERVIEW.md` |
| HW LP生成コマンド | `app/Console/Commands/GenerateHelloworkLps.php` |
| スケジュール設定 | `routes/console.php` |
| 施設向け管理ページ | `resources/views/jobs/manage.blade.php` |
| トライアル警告メール | `resources/views/emails/trial_ending_warning.blade.php` |
| 請求集計メール | `resources/views/emails/billing_summary.blade.php` |
| 特商法ページ | `resources/views/pages/legal.blade.php` |
| 利用規約 | `resources/views/pages/terms.blade.php` |
