<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\BillingAgreement;
use App\Models\BillingSummary;
use App\Models\Job;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CareEntryDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->createJobs();
    }

    // =========================================================
    // 求人データ作成
    // =========================================================

    private function createJobs(): void
    {
        // ----- シナリオA: 無料掲載中・有効応募2件（まだ無料枠内） -----
        $jobA = $this->makeJob([
            'company_name'      => '会社A_無料掲載中',
            'title'             => '介護スタッフ_日勤',
            'contact_email'     => 'company-a@demo.test',
            'contact_phone'     => '0901111001',
            'status'            => Job::STATUS_ACTIVE,
            'email_verified_at' => now()->subDays(30),
            'expires_at'        => now()->addMonths(2),
            'salary_type'       => 'monthly',
            'salary_min'        => 200000,
            'salary_max'        => 250000,
            'admin_memo'        => null,
        ]);
        $this->createBillingAgreement($jobA);
        // 有効応募 2件（無料枠内）
        $this->makeApp($jobA, '有効応募_山田太郎', 'yamada-taro@demo.test', '09011110001', Application::TYPE_FORM, true, false, Application::STATUS_CONFIRMED);
        $this->makeApp($jobA, '有効応募_佐藤花子', 'sato-hanako@demo.test', '09011110002', Application::TYPE_FORM, true, false, Application::STATUS_RECEIVED);
        // 無効応募 1件（テスト）
        $this->makeApp($jobA, '無効応募_テスト太郎', 'test@demo.test', '09011110003', Application::TYPE_FORM, false, false, Application::STATUS_RECEIVED, Application::INVALID_TEST);
        // 重複応募 1件
        $this->makeApp($jobA, '無効応募_重複応募', 'yamada-taro@demo.test', '09011110001', Application::TYPE_FORM, false, false, Application::STATUS_RECEIVED, Application::INVALID_DUPLICATE);

        // ----- シナリオB: 終了間近（5日後に期限切れ） -----
        $jobB = $this->makeJob([
            'company_name'      => '会社B_終了間近',
            'title'             => '訪問介護ヘルパー',
            'contact_email'     => 'company-b@demo.test',
            'contact_phone'     => '0901111011',
            'status'            => Job::STATUS_ACTIVE,
            'email_verified_at' => now()->subMonths(3)->addDays(2),
            'expires_at'        => now()->addDays(5),
            'salary_type'       => 'hourly',
            'salary_min'        => 1200,
            'salary_max'        => 1500,
            'admin_memo'        => null,
        ]);
        $this->createBillingAgreement($jobB);
        $this->makeApp($jobB, '有効応募_比嘉一郎', 'higa-ichiro@demo.test', '09011110011', Application::TYPE_LINE, true, false, Application::STATUS_CONFIRMED);
        $this->makeApp($jobB, '有効応募_大城花子', 'oshiro-hanako@demo.test', '09011110012', Application::TYPE_FORM, true, false, Application::STATUS_RECEIVED);

        // ----- シナリオC: 期限切れ -----
        $jobC = $this->makeJob([
            'company_name'      => '会社C_期限切れ',
            'title'             => 'デイサービス職員',
            'contact_email'     => 'company-c@demo.test',
            'contact_phone'     => '0901111021',
            'status'            => Job::STATUS_EXPIRED,
            'email_verified_at' => now()->subMonths(4),
            'expires_at'        => now()->subDays(10),
            'salary_type'       => 'monthly',
            'salary_min'        => 210000,
            'salary_max'        => 260000,
            'admin_memo'        => null,
        ]);
        $this->createBillingAgreement($jobC);
        $this->makeApp($jobC, '有効応募_新垣太郎', 'aragaki-taro@demo.test', '09011110021', Application::TYPE_FORM, true, false, Application::STATUS_CONFIRMED);

        // ----- シナリオD: 継続済み（1件目） -----
        $jobD = $this->makeJob([
            'company_name'      => '会社D_継続済み',
            'title'             => '介護スタッフ_夜勤',
            'contact_email'     => 'company-d@demo.test',
            'contact_phone'     => '0901111031',
            'status'            => Job::STATUS_ACTIVE,
            'email_verified_at' => now()->subMonths(4),
            'expires_at'        => now()->subDays(5),
            'continued_at'      => now()->subDays(3),
            'salary_type'       => 'daily',
            'salary_min'        => 12000,
            'salary_max'        => 15000,
            'admin_memo'        => '継続済み確認用 ' . now()->format('Y-m-d'),
        ]);
        $this->createBillingAgreement($jobD);
        $this->makeApp($jobD, '有効応募_仲間花子', 'nakama-hanako@demo.test', '09011110031', Application::TYPE_LINE, true, false, Application::STATUS_CONFIRMED);
        $this->makeApp($jobD, '有効応募_玉城一郎', 'tamaki-ichiro@demo.test', '09011110032', Application::TYPE_FORM, true, false, Application::STATUS_CONFIRMED);

        // ----- シナリオE: 一時停止 -----
        $jobE = $this->makeJob([
            'company_name'      => '会社E_一時停止',
            'title'             => '看護スタッフ',
            'contact_email'     => 'company-e@demo.test',
            'contact_phone'     => '0901111041',
            'status'            => Job::STATUS_PAUSED,
            'email_verified_at' => now()->subDays(45),
            'expires_at'        => now()->addMonths(2),
            'paused_at'         => now()->subDays(7),
            'salary_type'       => 'monthly',
            'salary_min'        => 220000,
            'salary_max'        => 280000,
            'admin_memo'        => null,
        ]);
        $this->createBillingAgreement($jobE);
        $this->makeApp($jobE, '有効応募_宮城太郎', 'miyagi-taro@demo.test', '09011110041', Application::TYPE_FORM, true, false, Application::STATUS_RECEIVED);

        // ----- シナリオF: 管理者非表示 -----
        $jobF = $this->makeJob([
            'company_name'      => '会社F_管理者非表示',
            'title'             => '生活相談員',
            'contact_email'     => 'company-f@demo.test',
            'contact_phone'     => '0901111051',
            'status'            => Job::STATUS_ACTIVE,
            'email_verified_at' => now()->subDays(20),
            'expires_at'        => now()->addMonths(2),
            'is_admin_hidden'   => true,
            'admin_memo'        => '非表示テスト用 スパム疑い ' . now()->format('Y-m-d'),
            'salary_type'       => 'hourly',
            'salary_min'        => 1100,
            'salary_max'        => 1300,
        ]);
        $this->createBillingAgreement($jobF);
        $this->makeApp($jobF, '無効応募_スパム太郎', 'spam-bot@demo.test', '09011110051', Application::TYPE_FORM, false, false, Application::STATUS_RECEIVED, Application::INVALID_SPAM);

        // ----- シナリオG: 課金中（無料枠3件消費済み → 4件目から課金） -----
        $jobG = $this->makeJob([
            'company_name'      => '会社G_課金中',
            'title'             => '介護スタッフ_日勤',
            'contact_email'     => 'company-g@demo.test',
            'contact_phone'     => '0901111061',
            'status'            => Job::STATUS_ACTIVE,
            'email_verified_at' => now()->subMonths(2),
            'expires_at'        => now()->addMonths(1),
            'salary_type'       => 'monthly',
            'salary_min'        => 200000,
            'salary_max'        => 240000,
            'admin_memo'        => '請求確認用',
        ]);
        $this->createBillingAgreement($jobG);
        // 無料枠3件（is_billable = false）
        $this->makeApp($jobG, '有効応募_照屋一郎', 'teruya-ichiro@demo.test', '09011110061', Application::TYPE_FORM, true, false, Application::STATUS_CONFIRMED, null, now()->subDays(50));
        $this->makeApp($jobG, '有効応募_知念花子', 'chinen-hanako@demo.test', '09011110062', Application::TYPE_LINE, true, false, Application::STATUS_CONFIRMED, null, now()->subDays(40));
        $this->makeApp($jobG, '有効応募_上原太郎', 'uehara-taro@demo.test', '09011110063', Application::TYPE_FORM, true, false, Application::STATUS_CONFIRMED, null, now()->subDays(30));
        // 無料枠到達後：課金対象（is_billable = true）
        $this->makeApp($jobG, '有効応募_金城美咲', 'kinjo-misaki@demo.test', '09011110064', Application::TYPE_FORM, true, true, Application::STATUS_CONFIRMED, null, now()->subDays(20));
        $this->makeApp($jobG, 'LINE応募_比嘉二郎', 'higa-jiro@demo.test', '09011110065', Application::TYPE_LINE, true, true, Application::STATUS_RECEIVED, null, now()->subDays(10));
        // 無効応募
        $this->makeApp($jobG, '無効応募_重複チェック', 'teruya-ichiro@demo.test', '09011110061', Application::TYPE_FORM, false, false, Application::STATUS_RECEIVED, Application::INVALID_DUPLICATE, now()->subDays(45));

        // ----- シナリオH: メール未認証 -----
        $jobH = $this->makeJob([
            'company_name'      => '会社H_メール未認証',
            'title'             => '訪問介護ヘルパー',
            'contact_email'     => 'company-h@demo.test',
            'contact_phone'     => '0901111071',
            'status'            => Job::STATUS_PENDING,
            'email_verified_at' => null,
            'expires_at'        => null,
            'salary_type'       => 'hourly',
            'salary_min'        => 1200,
            'salary_max'        => 1400,
            'admin_memo'        => null,
        ]);

        // ----- シナリオI: 応募ゼロ -----
        $jobI = $this->makeJob([
            'company_name'      => '会社I_応募ゼロ',
            'title'             => 'デイサービス職員',
            'contact_email'     => 'company-i@demo.test',
            'contact_phone'     => '0901111081',
            'status'            => Job::STATUS_ACTIVE,
            'email_verified_at' => now()->subDays(10),
            'expires_at'        => now()->addMonths(3),
            'salary_type'       => 'daily',
            'salary_min'        => 11000,
            'salary_max'        => 14000,
            'admin_memo'        => null,
        ]);
        $this->createBillingAgreement($jobI);

        // ----- シナリオJ: 請求確認用（トライアル期限切れ → 全応募が課金対象） -----
        $jobJ = $this->makeJob([
            'company_name'      => '会社J_請求確認用',
            'title'             => '介護スタッフ_夜勤',
            'contact_email'     => 'company-j@demo.test',
            'contact_phone'     => '0901111091',
            'status'            => Job::STATUS_ACTIVE,
            'email_verified_at' => now()->subMonths(5),
            'expires_at'        => now()->subMonths(2),
            'salary_type'       => 'monthly',
            'salary_min'        => 215000,
            'salary_max'        => 260000,
            'admin_memo'        => '請求確認用',
        ]);
        $this->createBillingAgreement($jobJ);
        // 全応募が課金対象（トライアル期限超過後）
        $this->makeApp($jobJ, '有効応募_田中美咲', 'tanaka-misaki@demo.test', '09011110091', Application::TYPE_FORM, true, true, Application::STATUS_CONFIRMED, null, now()->subMonths(3));
        $this->makeApp($jobJ, '有効応募_川村一郎', 'kawamura-ichiro@demo.test', '09011110092', Application::TYPE_LINE, true, true, Application::STATUS_CONFIRMED, null, now()->subMonths(2)->addDays(5));
        $this->makeApp($jobJ, '有効応募_伊藤花子', 'ito-hanako@demo.test', '09011110093', Application::TYPE_FORM, true, true, Application::STATUS_RECEIVED, null, now()->subDays(15));
        $this->makeApp($jobJ, '有効応募_渡辺太郎', 'watanabe-taro@demo.test', '09011110094', Application::TYPE_FORM, true, true, Application::STATUS_RECEIVED, null, now()->subDays(5));
        $this->makeApp($jobJ, '無効応募_入力不備', 'nofield@demo.test', '', Application::TYPE_FORM, false, false, Application::STATUS_RECEIVED, Application::INVALID_MISSING_FIELDS, now()->subDays(20));
        // 管理者が有効/無効切り替えしたくなる応募
        $this->makeApp($jobJ, 'Web応募_島袋美咲', 'shimabukuro@demo.test', '09011110096', Application::TYPE_FORM, true, true, Application::STATUS_RECEIVED, null, now()->subDays(2));

        // ----- シナリオK: 継続済み（2件目） -----
        $jobK = $this->makeJob([
            'company_name'      => '会社K_継続済み2',
            'title'             => '看護スタッフ',
            'contact_email'     => 'company-k@demo.test',
            'contact_phone'     => '0901111101',
            'status'            => Job::STATUS_ACTIVE,
            'email_verified_at' => now()->subMonths(4)->addDays(5),
            'expires_at'        => now()->subDays(2),
            'continued_at'      => now()->subDay(),
            'salary_type'       => 'monthly',
            'salary_min'        => 230000,
            'salary_max'        => 280000,
            'admin_memo'        => '継続済み確認用（2件目）',
        ]);
        $this->createBillingAgreement($jobK);
        $this->makeApp($jobK, '有効応募_宮里一郎', 'miyasato-ichiro@demo.test', '09011110101', Application::TYPE_LINE, true, false, Application::STATUS_CONFIRMED);

        // ----- シナリオL: 終了間近（3日後に期限切れ） -----
        $jobL = $this->makeJob([
            'company_name'      => '会社L_終了間近2',
            'title'             => '介護スタッフ_日勤',
            'contact_email'     => 'company-l@demo.test',
            'contact_phone'     => '0901111111',
            'status'            => Job::STATUS_ACTIVE,
            'email_verified_at' => now()->subMonths(3)->addDays(4),
            'expires_at'        => now()->addDays(3),
            'salary_type'       => 'hourly',
            'salary_min'        => 1300,
            'salary_max'        => 1600,
            'admin_memo'        => null,
        ]);
        $this->createBillingAgreement($jobL);
        $this->makeApp($jobL, 'LINE応募_仲村花子', 'nakamura-hanako@demo.test', '09011111112', Application::TYPE_LINE, true, false, Application::STATUS_RECEIVED);
        $this->makeApp($jobL, 'Web応募_大浜太郎', 'ohama-taro@demo.test', '09011111113', Application::TYPE_FORM, true, false, Application::STATUS_RECEIVED);

        // ----- 請求サマリー作成 -----
        $this->createBillingSummaries($jobG, $jobJ);
    }

    // =========================================================
    // 請求サマリー作成
    // =========================================================

    private function createBillingSummaries(Job $jobG, Job $jobJ): void
    {
        // 送付済み（会社G: 先月分）
        BillingSummary::create([
            'contact_email'  => $jobG->contact_email,
            'billing_month'  => now()->subMonth()->format('Y-m'),
            'valid_count'    => 5,
            'billable_count' => 2,
            'unit_price'     => 3000,
            'total_amount'   => 6000,
            'status'         => BillingSummary::STATUS_SENT,
            'sent_at'        => now()->subDays(5),
        ]);

        // 入金済み（会社J: 先々月分）
        BillingSummary::create([
            'contact_email'  => $jobJ->contact_email,
            'billing_month'  => now()->subMonths(2)->format('Y-m'),
            'valid_count'    => 8,
            'billable_count' => 5,
            'unit_price'     => 3000,
            'total_amount'   => 15000,
            'status'         => BillingSummary::STATUS_PAID,
            'sent_at'        => now()->subDays(45),
        ]);

        // 期限超過（会社J: 3ヶ月前分）
        BillingSummary::create([
            'contact_email'  => $jobJ->contact_email,
            'billing_month'  => now()->subMonths(3)->format('Y-m'),
            'valid_count'    => 3,
            'billable_count' => 3,
            'unit_price'     => 3000,
            'total_amount'   => 9000,
            'status'         => BillingSummary::STATUS_OVERDUE,
            'sent_at'        => now()->subDays(70),
        ]);

        // 未払い（会社G: 2ヶ月前分）
        BillingSummary::create([
            'contact_email'  => $jobG->contact_email,
            'billing_month'  => now()->subMonths(2)->format('Y-m'),
            'valid_count'    => 4,
            'billable_count' => 1,
            'unit_price'     => 3000,
            'total_amount'   => 3000,
            'status'         => BillingSummary::STATUS_UNPAID,
            'sent_at'        => now()->subDays(35),
        ]);

        // 保留（会社J: 先月分）
        BillingSummary::create([
            'contact_email'  => $jobJ->contact_email,
            'billing_month'  => now()->subMonth()->format('Y-m'),
            'valid_count'    => 6,
            'billable_count' => 4,
            'unit_price'     => 3000,
            'total_amount'   => 12000,
            'status'         => BillingSummary::STATUS_ON_HOLD,
            'sent_at'        => now()->subDays(3),
        ]);
    }

    // =========================================================
    // ヘルパー
    // =========================================================

    private function makeJob(array $attrs): Job
    {
        return Job::create(array_merge([
            'token'              => Str::random(32),
            'is_admin_hidden'    => false,
            'paused_at'          => null,
            'continued_at'       => null,
            'trial_warning_sent_at' => null,
            'expired_notified_at'   => null,
            'seo_title'          => null,
            'meta_description'   => null,
            'description_generated' => null,
        ], $attrs));
    }

    private function makeApp(
        Job    $job,
        string $name,
        ?string $email,
        ?string $phone,
        string $type,
        bool   $isValid,
        bool   $isBillable,
        string $status,
        ?string $invalidReason = null,
        ?\Carbon\Carbon $appliedAt = null,
    ): Application {
        $appliedAt ??= now()->subDays(fake()->numberBetween(1, 30));

        return Application::create([
            'job_id'            => $job->id,
            'application_type'  => $type,
            'applicant_name'    => $name,
            'email'             => $email,
            'normalized_email'  => $email ? strtolower($email) : null,
            'phone'             => $phone ?? '',
            'normalized_phone'  => $phone ? preg_replace('/[^0-9]/', '', $phone) : null,
            'status'            => $status,
            'applied_at'        => $appliedAt,
            'is_valid'          => $isValid,
            'invalid_reason'    => $invalidReason,
            'is_billable'       => $isBillable,
            'billable_snapshot' => $isBillable,
            'counted_at'        => $isValid ? $appliedAt : null,
        ]);
    }

    private function createBillingAgreement(Job $job): void
    {
        BillingAgreement::create([
            'job_id'                  => $job->id,
            'agreement_flag'          => true,
            'agreement_text'          => '3ヶ月間または有効応募3件まで無料。以降は有効応募1件につき3,000円（税込）。',
            'agreement_text_version'  => 'v1.1',
            'agreed_at'               => $job->created_at ?? now(),
            'agreed_ip'               => '127.0.0.1',
            'user_agent'              => 'DemoSeeder/1.0',
        ]);
    }
}
