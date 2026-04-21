<?php

namespace Database\Factories;

use App\Models\BillingSummary;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BillingSummary>
 */
class BillingSummaryFactory extends Factory
{
    protected $model = BillingSummary::class;

    public function definition(): array
    {
        $billableCount = fake()->numberBetween(1, 5);

        return [
            'contact_email'  => fake()->safeEmail(),
            'billing_month'  => now()->subMonth()->format('Y-m'),
            'valid_count'    => $billableCount + fake()->numberBetween(0, 3),
            'billable_count' => $billableCount,
            'unit_price'     => 3000,
            'total_amount'   => $billableCount * 3000,
            'status'         => BillingSummary::STATUS_SENT,
            'sent_at'        => now()->subDays(5),
        ];
    }

    public function unbilled(): static
    {
        return $this->state(fn () => [
            'status'  => BillingSummary::STATUS_UNBILLED,
            'sent_at' => null,
        ]);
    }

    public function sent(): static
    {
        return $this->state(fn () => [
            'status'  => BillingSummary::STATUS_SENT,
            'sent_at' => now()->subDays(fake()->numberBetween(1, 10)),
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn () => [
            'status'  => BillingSummary::STATUS_PAID,
            'sent_at' => now()->subDays(20),
        ]);
    }

    public function unpaid(): static
    {
        return $this->state(fn () => [
            'status'  => BillingSummary::STATUS_UNPAID,
            'sent_at' => now()->subDays(15),
        ]);
    }

    public function onHold(): static
    {
        return $this->state(fn () => [
            'status'  => BillingSummary::STATUS_ON_HOLD,
            'sent_at' => now()->subDays(10),
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn () => [
            'status'  => BillingSummary::STATUS_OVERDUE,
            'sent_at' => now()->subDays(40),
        ]);
    }
}
