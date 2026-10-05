<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_request_id' => ServiceRequest::factory()->state([
                'status' => ServiceRequest::STATUS_AWAITING_PAYMENT,
                'fee_amount' => 50,
            ]),
            'provider' => Payment::PROVIDER_DEMO_QRPH,
            'amount' => 50,
            'status' => Payment::STATUS_PENDING,
            'expires_at' => now()->addHour(),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (): array => [
            'status' => Payment::STATUS_PAID,
            'paid_at' => now(),
        ]);
    }

    public function cash(): static
    {
        return $this->state(fn (): array => [
            'provider' => Payment::PROVIDER_CASH,
            'expires_at' => null,
        ]);
    }

    public function paymongo(): static
    {
        return $this->state(fn (): array => [
            'provider' => Payment::PROVIDER_PAYMONGO_QRPH,
            'provider_reference' => 'cs_'.Str::random(24),
            'provider_livemode' => true,
            'checkout_url' => fn (array $attributes): string => 'https://checkout.paymongo.com/'.$attributes['provider_reference'],
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (): array => [
            'status' => Payment::STATUS_EXPIRED,
            'expires_at' => now()->subMinute(),
        ]);
    }
}
