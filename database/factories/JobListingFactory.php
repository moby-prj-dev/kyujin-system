<?php

namespace Database\Factories;

use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Job>
 */
class JobListingFactory extends Factory
{
    protected $model = Job::class;

    public function definition(): array
    {
        return [
            'token'              => Str::random(32),
            'company_name'       => fake('ja_JP')->company(),
            'title'              => fake('ja_JP')->randomElement([
                '介護スタッフ（日勤）',
                '介護スタッフ（夜勤）',
                'デイサービス職員',
                '訪問介護ヘルパー',
                '看護スタッフ',
                '生活相談員',
            ]),
            'contact_email'      => fake()->unique()->safeEmail(),
            'contact_phone'      => '090' . fake()->numerify('########'),
            'status'             => Job::STATUS_ACTIVE,
            'email_verified_at'  => now()->subDays(30),
            'expires_at'         => now()->addMonths(3)->subDays(30),
            'paused_at'          => null,
            'continued_at'       => null,
            'salary_type'        => fake()->randomElement(['monthly', 'hourly', 'daily']),
            'salary_min'         => fake()->randomElement([200000, 1200, 12000]),
            'salary_max'         => fake()->randomElement([250000, 1500, 15000]),
            'is_admin_hidden'    => false,
            'admin_memo'         => null,
            'seo_title'          => null,
            'meta_description'   => null,
            'description_generated' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status'            => Job::STATUS_ACTIVE,
            'email_verified_at' => now()->subDays(30),
            'expires_at'        => now()->addMonths(2),
            'paused_at'         => null,
        ]);
    }

    public function paused(): static
    {
        return $this->state(fn () => [
            'status'            => Job::STATUS_PAUSED,
            'email_verified_at' => now()->subDays(45),
            'expires_at'        => now()->addMonths(2),
            'paused_at'         => now()->subDays(7),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'status'            => Job::STATUS_EXPIRED,
            'email_verified_at' => now()->subMonths(4),
            'expires_at'        => now()->subDays(10),
            'paused_at'         => null,
        ]);
    }

    public function hidden(): static
    {
        return $this->state(fn () => [
            'status'            => Job::STATUS_ACTIVE,
            'email_verified_at' => now()->subDays(20),
            'expires_at'        => now()->addMonths(2),
            'is_admin_hidden'   => true,
            'admin_memo'        => '【管理者】スパム疑いのため非表示設定 ' . now()->format('Y-m-d'),
        ]);
    }

    public function continued(): static
    {
        return $this->state(fn () => [
            'status'            => Job::STATUS_ACTIVE,
            'email_verified_at' => now()->subMonths(4),
            'expires_at'        => now()->subDays(5),
            'continued_at'      => now()->subDays(3),
            'admin_memo'        => '継続済み確認用 ' . now()->format('Y-m-d'),
        ]);
    }

    public function endingSoon(): static
    {
        return $this->state(fn () => [
            'status'            => Job::STATUS_ACTIVE,
            'email_verified_at' => now()->subMonths(3)->addDays(2),
            'expires_at'        => now()->addDays(fake()->numberBetween(2, 6)),
            'paused_at'         => null,
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn () => [
            'status'            => Job::STATUS_PENDING,
            'email_verified_at' => null,
            'expires_at'        => null,
        ]);
    }
}
