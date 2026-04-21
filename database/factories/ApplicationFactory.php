<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Application>
 */
class ApplicationFactory extends Factory
{
    protected $model = Application::class;

    public function definition(): array
    {
        $email = fake()->unique()->safeEmail();
        $phone = '090' . fake()->numerify('########');

        return [
            'job_id'            => Job::factory(),
            'application_type'  => Application::TYPE_FORM,
            'applicant_name'    => fake('ja_JP')->name(),
            'email'             => $email,
            'normalized_email'  => strtolower($email),
            'phone'             => $phone,
            'normalized_phone'  => preg_replace('/[^0-9]/', '', $phone),
            'status'            => Application::STATUS_RECEIVED,
            'applied_at'        => now()->subDays(fake()->numberBetween(1, 60)),
            'is_valid'          => true,
            'invalid_reason'    => null,
            'is_billable'       => false,
            'billable_snapshot' => false,
            'counted_at'        => now()->subDays(fake()->numberBetween(1, 60)),
        ];
    }

    public function valid(): static
    {
        return $this->state(fn () => [
            'is_valid'          => true,
            'invalid_reason'    => null,
            'counted_at'        => now()->subDays(fake()->numberBetween(1, 30)),
        ]);
    }

    public function invalid(string $reason = Application::INVALID_MANUAL): static
    {
        return $this->state(fn () => [
            'is_valid'          => false,
            'invalid_reason'    => $reason,
            'is_billable'       => false,
            'billable_snapshot' => false,
            'counted_at'        => null,
        ]);
    }

    public function billable(): static
    {
        return $this->state(fn () => [
            'is_valid'          => true,
            'invalid_reason'    => null,
            'is_billable'       => true,
            'billable_snapshot' => true,
            'counted_at'        => now()->subDays(fake()->numberBetween(1, 30)),
        ]);
    }

    public function freeTrial(): static
    {
        return $this->state(fn () => [
            'is_valid'          => true,
            'invalid_reason'    => null,
            'is_billable'       => false,
            'billable_snapshot' => false,
            'counted_at'        => now()->subDays(fake()->numberBetween(1, 30)),
        ]);
    }

    public function formApply(): static
    {
        return $this->state(fn () => [
            'application_type' => Application::TYPE_FORM,
        ]);
    }

    public function lineApply(): static
    {
        return $this->state(fn () => [
            'application_type' => Application::TYPE_LINE,
        ]);
    }

    public function received(): static
    {
        return $this->state(fn () => ['status' => Application::STATUS_RECEIVED]);
    }

    public function confirmed(): static
    {
        return $this->state(fn () => ['status' => Application::STATUS_CONFIRMED]);
    }

    public function closed(): static
    {
        return $this->state(fn () => ['status' => Application::STATUS_CLOSED]);
    }
}
