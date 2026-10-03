<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Payment;
use App\Models\Resident;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class DemoPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_approval_with_a_fee_waits_for_demo_payment(): void
    {
        $serviceRequest = ServiceRequest::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('service-requests.review', $serviceRequest), [
                'decision' => 'complete',
                'response_note' => 'Use the QRPH payment below.',
            ])->assertRedirect(route('service-requests.show', $serviceRequest));

        $serviceRequest->refresh();
        $this->assertSame(ServiceRequest::STATUS_AWAITING_PAYMENT, $serviceRequest->status);
        $this->assertSame('50.00', $serviceRequest->fee_amount);
        $this->assertNull($serviceRequest->certificate_id);
        $this->assertDatabaseCount('certificates', 0);
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_resident_can_generate_one_active_demo_qr_payment(): void
    {
        $this->freezeTime();
        [$residentUser, $serviceRequest] = $this->awaitingPaymentRequest();

        $this->actingAs($residentUser)
            ->post(route('account.requests.demo-payment.store', $serviceRequest))
            ->assertRedirect(route('account.requests.show', $serviceRequest));
        $this->post(route('account.requests.demo-payment.store', $serviceRequest))->assertRedirect();

        $payment = Payment::query()->sole();
        $this->assertSame(Payment::STATUS_PENDING, $payment->status);
        $this->assertSame('50.00', $payment->amount);

        $this->get(route('account.requests.show', $serviceRequest))
            ->assertOk()
            ->assertSee('QRPH PAYMENT')
            ->assertSee('Pay cash instead')
            ->assertSeeText('Scan the QRPH code below with your phone to continue payment.')
            ->assertDontSee('Demo')
            ->assertDontSee('simulate', false)
            ->assertSee('data-demo-payment-qr', false);
    }

    public function test_resident_can_choose_cash_at_the_barangay_hall(): void
    {
        [$residentUser, $serviceRequest] = $this->awaitingPaymentRequest();

        $this->actingAs($residentUser)
            ->get(route('account.requests.show', $serviceRequest))
            ->assertOk()
            ->assertSee('QRPH Payment')
            ->assertSee('Cash at Barangay Hall')
            ->assertSeeText('Choose QRPH or cash at the barangay hall below to complete your payment.')
            ->assertDontSeeText('Generate and scan the QRPH code below to complete the payment.');

        $this->post(route('account.requests.cash-payment.store', $serviceRequest))
            ->assertRedirect(route('account.requests.show', $serviceRequest));

        $payment = Payment::query()->sole();
        $this->assertSame(Payment::PROVIDER_CASH, $payment->provider);
        $this->assertSame(Payment::STATUS_PENDING, $payment->status);
        $this->assertSame('50.00', $payment->amount);
        $this->assertNull($payment->expires_at);

        $this->get(route('account.requests.show', $serviceRequest))->assertOk()
            ->assertSee('CASH PAYMENT')
            ->assertSee('Waiting for staff confirmation')
            ->assertSee('Switch to QRPH')
            ->assertSeeText('Pay cash at the barangay hall and present your request number. Staff will confirm your payment.')
            ->assertDontSeeText('Scan the QRPH code below with your phone to continue payment.')
            ->assertDontSeeText('Generate and scan the QRPH code below to complete the payment.');

        $this->get(route('account'))
            ->assertSeeText('Request approved. Open it to view your payment options and instructions.')
            ->assertDontSeeText('Open it to generate the QRPH payment.');
    }

    public function test_staff_records_cash_payment_and_issues_the_certificate(): void
    {
        [$residentUser, $serviceRequest] = $this->awaitingPaymentRequest();
        $this->actingAs($residentUser)->post(route('account.requests.cash-payment.store', $serviceRequest));
        $payment = Payment::query()->sole();
        $staff = User::factory()->create();

        $this->actingAs($staff)
            ->get(route('service-requests.show', $serviceRequest))
            ->assertOk()->assertSee('Record cash payment');
        $this->post(route('payments.cash.confirm', $payment), [])
            ->assertSessionHasErrors('receipt_number');
        $this->post(route('payments.cash.confirm', $payment), [
            'receipt_number' => 'OR-2026-00125',
            'payment_note' => 'Paid at the barangay hall cashier.',
        ])->assertRedirect(route('service-requests.show', $serviceRequest));

        $payment->refresh();
        $serviceRequest->refresh();
        $certificate = Certificate::query()->sole();
        $this->assertSame(Payment::STATUS_PAID, $payment->status);
        $this->assertSame('OR-2026-00125', $payment->receipt_number);
        $this->assertSame($staff->id, $payment->recorded_by);
        $this->assertNotNull($payment->paid_at);
        $this->assertSame(ServiceRequest::STATUS_COMPLETED, $serviceRequest->status);
        $this->assertSame($certificate->id, $serviceRequest->certificate_id);
        $this->assertSame('50.00', $certificate->fee);
    }

    public function test_resident_can_switch_from_cash_to_qrph_before_payment(): void
    {
        $this->freezeTime();
        [$residentUser, $serviceRequest] = $this->awaitingPaymentRequest();
        $this->actingAs($residentUser)->post(route('account.requests.cash-payment.store', $serviceRequest));
        $cashPayment = Payment::query()->sole();

        $this->post(route('account.requests.demo-payment.store', $serviceRequest))->assertRedirect();

        $this->assertSame(Payment::STATUS_CANCELLED, $cashPayment->fresh()->status);
        $this->assertSame(2, $serviceRequest->payments()->count());
        $this->assertSame(
            Payment::PROVIDER_DEMO_QRPH,
            $serviceRequest->payments()->latest('id')->firstOrFail()->provider,
        );

        $this->get(route('account.requests.show', $serviceRequest))
            ->assertSeeText('Scan the QRPH code below with your phone to continue payment.')
            ->assertDontSeeText('Pay cash at the barangay hall and present your request number. Staff will confirm your payment.');
    }

    public function test_only_the_request_owner_can_create_or_check_a_payment(): void
    {
        [$owner, $serviceRequest] = $this->awaitingPaymentRequest();
        $otherResident = Resident::factory()->create();
        $otherUser = User::factory()->resident()->create(['resident_id' => $otherResident->id]);

        $this->actingAs($otherUser)
            ->post(route('account.requests.demo-payment.store', $serviceRequest))
            ->assertNotFound();

        $this->actingAs($owner)->post(route('account.requests.demo-payment.store', $serviceRequest));
        $payment = Payment::query()->sole();

        $this->actingAs($otherUser)
            ->getJson(route('account.demo-payments.status', $payment))
            ->assertNotFound();
        $this->actingAs($owner)
            ->getJson(route('account.demo-payments.status', $payment))
            ->assertOk()->assertJsonPath('status', Payment::STATUS_PENDING);
    }

    public function test_demo_payment_page_requires_a_valid_signed_link(): void
    {
        $payment = Payment::factory()->create();
        $signedUrl = URL::temporarySignedRoute(
            'demo-payments.show',
            now()->addHour(),
            ['payment' => $payment],
            absolute: false,
        );

        $this->get(route('demo-payments.show', $payment))->assertForbidden();
        $this->get($signedUrl)->assertOk()
            ->assertSee('QRPH Payment')
            ->assertSee('Confirm payment')
            ->assertSee('data-submit-label', false)
            ->assertSee('data-loading-label="Confirming payment…"', false)
            ->assertDontSee('Demo')
            ->assertDontSee('simulate', false);
    }

    public function test_confirming_demo_payment_issues_one_certificate_and_completes_the_request(): void
    {
        $payment = Payment::factory()->create(['amount' => 80]);
        $confirmUrl = URL::temporarySignedRoute(
            'demo-payments.confirm',
            now()->addHour(),
            ['payment' => $payment],
            absolute: false,
        );

        $this->post($confirmUrl)->assertRedirect();
        $this->post($confirmUrl)->assertRedirect();

        $serviceRequest = $payment->serviceRequest->fresh();
        $certificate = Certificate::query()->sole();
        $this->assertSame(ServiceRequest::STATUS_COMPLETED, $serviceRequest->status);
        $this->assertSame($certificate->id, $serviceRequest->certificate_id);
        $this->assertSame($serviceRequest->resident_id, $certificate->resident_id);
        $this->assertSame('80.00', $certificate->fee);
        $this->assertSame(Payment::STATUS_PAID, $payment->fresh()->status);
        $this->assertNotNull($payment->fresh()->paid_at);
        $this->assertDatabaseCount('certificates', 1);
    }

    public function test_an_expired_payment_is_replaced_with_a_new_qr_session(): void
    {
        $this->freezeTime();
        [$residentUser, $serviceRequest] = $this->awaitingPaymentRequest();
        $expiredPayment = Payment::factory()->expired()->for($serviceRequest)->create();

        $this->actingAs($residentUser)
            ->get(route('account.requests.show', $serviceRequest))
            ->assertSeeText('Choose QRPH or cash at the barangay hall below to complete your payment.')
            ->assertSeeText('The previous QR code expired. Generate a new one or choose cash at the barangay hall.')
            ->assertDontSeeText('Scan the QRPH code below with your phone to continue payment.');

        $this
            ->post(route('account.requests.demo-payment.store', $serviceRequest))
            ->assertRedirect();

        $this->assertSame(Payment::STATUS_EXPIRED, $expiredPayment->fresh()->status);
        $this->assertSame(2, $serviceRequest->payments()->count());
        $this->assertSame(Payment::STATUS_PENDING, $serviceRequest->payments()->latest('id')->firstOrFail()->status);
    }

    /**
     * @return array{User, ServiceRequest}
     */
    private function awaitingPaymentRequest(): array
    {
        $resident = Resident::factory()->create();
        $residentUser = User::factory()->resident()->create(['resident_id' => $resident->id]);
        $serviceRequest = ServiceRequest::factory()->for($resident)->create([
            'status' => ServiceRequest::STATUS_AWAITING_PAYMENT,
            'fee_amount' => 50,
            'reviewed_at' => now(),
        ]);

        return [$residentUser, $serviceRequest];
    }
}
