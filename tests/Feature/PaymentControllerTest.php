<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Payment;
use App\Models\Resident;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'payments.public_url' => 'https://bis.example.test',
            'payments.paymongo.mode' => 'live',
            'payments.paymongo.secret_key' => 'sk_live_fixture',
            'payments.paymongo.webhook_secret' => 'whsec_fixture',
        ]);
        Http::preventStrayRequests();
    }

    public function test_staff_approval_with_a_fee_waits_for_payment(): void
    {
        $serviceRequest = ServiceRequest::factory()->create();

        $this->actingAs(User::factory()->create())->post(route('service-requests.review', $serviceRequest), [
            'decision' => 'complete',
            'response_note' => 'Please pay through QR Ph.',
        ])->assertRedirect(route('service-requests.show', $serviceRequest));

        $this->assertSame(ServiceRequest::STATUS_AWAITING_PAYMENT, $serviceRequest->fresh()->status);
        $this->assertSame('50.00', $serviceRequest->fresh()->fee_amount);
        $this->assertDatabaseCount('certificates', 0);
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_resident_starts_one_checkout_using_the_server_fee_and_only_qr_ph(): void
    {
        [$user, $serviceRequest] = $this->awaitingPaymentRequest();
        $this->fakeCreateCheckout();

        $this->actingAs($user)->post(route('account.requests.payment.store', $serviceRequest), [
            'amount' => 1, 'fee_amount' => 1, 'status' => 'paid',
        ])->assertRedirect('https://checkout.paymongo.com/cs_fixture');
        $this->post(route('account.requests.payment.store', $serviceRequest))
            ->assertRedirect('https://checkout.paymongo.com/cs_fixture');

        $payment = Payment::query()->sole();
        $this->assertSame(Payment::PROVIDER_PAYMONGO_QRPH, $payment->provider);
        $this->assertSame(Payment::STATUS_PENDING, $payment->status);
        $this->assertSame('50.00', $payment->amount);
        $this->assertTrue($payment->provider_livemode);
        Http::assertSentCount(1);
        Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
            && $request['data']['attributes']['line_items'][0]['amount'] === 5000
            && $request['data']['attributes']['line_items'][0]['currency'] === 'PHP'
            && $request['data']['attributes']['payment_method_types'] === ['qrph']
            && $request['data']['attributes']['reference_number'] === $payment->public_id
            && $request['data']['attributes']['pass_on_fees'] === false
            && $request['data']['attributes']['success_url'] === 'https://bis.example.test/account/payments/'.$payment->public_id.'/return');

        $this->get(route('account.requests.show', $serviceRequest))
            ->assertSeeText('Continue to checkout')->assertSeeText('Confirmation is automatic.')
            ->assertDontSeeText('Confirm payment')->assertDontSee('data-demo-payment-qr', false)
            ->assertDontSee('sk_live_fixture');
        $this->assertDatabaseCount('certificates', 0);
    }

    #[TestWith(['payments.paymongo.secret_key', ''])]
    #[TestWith(['payments.paymongo.secret_key', 'sk_test_fixture'])]
    #[TestWith(['payments.paymongo.webhook_secret', ''])]
    #[TestWith(['payments.public_url', 'http://127.0.0.1:8000'])]
    #[TestWith(['payments.public_url', 'https://192.168.68.108'])]
    public function test_unconfigured_online_payment_shows_feedback_without_creating_a_payment(string $setting, string $value): void
    {
        [$user, $serviceRequest] = $this->awaitingPaymentRequest();
        config([$setting => $value]);

        $this->actingAs($user)->from(route('account.requests.show', $serviceRequest))
            ->post(route('account.requests.payment.store', $serviceRequest))
            ->assertRedirect(route('account.requests.show', $serviceRequest))->assertSessionHasErrors('payment');

        $this->get(route('account.requests.show', $serviceRequest))->assertSeeText('Online payment is not available yet.');
        $this->assertDatabaseCount('payments', 0);
        Http::assertNothingSent();
    }

    public function test_checkout_failure_preserves_the_existing_cash_selection(): void
    {
        [$user, $serviceRequest] = $this->awaitingPaymentRequest();
        $cashPayment = Payment::factory()->cash()->for($serviceRequest)->create();
        Http::fake(['https://api.paymongo.com/v2/checkout_sessions' => Http::response(['errors' => []], 503)]);

        $this->actingAs($user)->from(route('account.requests.show', $serviceRequest))
            ->post(route('account.requests.payment.store', $serviceRequest))
            ->assertSessionHasErrors('payment');

        $this->assertDatabaseCount('payments', 1);
        $this->assertSame(Payment::STATUS_PENDING, $cashPayment->fresh()->status);
        $this->assertDatabaseCount('certificates', 0);
        Http::assertSentCount(1);
    }

    public function test_checkout_connection_failure_does_not_create_an_unusable_payment(): void
    {
        [$user, $serviceRequest] = $this->awaitingPaymentRequest();
        Http::fake(['https://api.paymongo.com/v2/checkout_sessions' => Http::failedConnection()]);

        $this->actingAs($user)->post(route('account.requests.payment.store', $serviceRequest))
            ->assertSessionHasErrors('payment');

        $this->assertDatabaseCount('payments', 0);
        $this->assertDatabaseCount('certificates', 0);
    }

    #[TestWith(['https://untrusted.example/pay', true])]
    #[TestWith(['https://checkout.paymongo.com/cs_fixture', false])]
    public function test_unexpected_checkout_host_or_mode_does_not_create_a_payment(string $checkoutUrl, bool $liveMode): void
    {
        [$user, $serviceRequest] = $this->awaitingPaymentRequest();
        Http::fake(['https://api.paymongo.com/v2/checkout_sessions' => Http::response([
            'data' => ['id' => 'cs_fixture', 'attributes' => ['checkout_url' => $checkoutUrl, 'livemode' => $liveMode]],
        ])]);

        $this->actingAs($user)->post(route('account.requests.payment.store', $serviceRequest))
            ->assertSessionHasErrors('payment');

        $this->assertDatabaseCount('payments', 0);
        Http::assertSentCount(1);
    }

    public function test_only_the_owner_can_start_check_or_return_from_checkout(): void
    {
        [$owner, $serviceRequest] = $this->awaitingPaymentRequest();
        $payment = Payment::factory()->paymongo()->for($serviceRequest)->create();
        $otherUser = User::factory()->resident()->create(['resident_id' => Resident::factory()->create()->id]);

        $this->actingAs($otherUser)->post(route('account.requests.payment.store', $serviceRequest))->assertNotFound();
        $this->getJson(route('account.payments.status', $payment))->assertNotFound();
        $this->get(route('account.payments.return', $payment))->assertNotFound();
        Http::assertNothingSent();
        $this->assertSame(Payment::STATUS_PENDING, $payment->fresh()->status);
    }

    public function test_guests_and_staff_cannot_start_resident_payments(): void
    {
        [, $serviceRequest] = $this->awaitingPaymentRequest();

        $this->post(route('account.requests.payment.store', $serviceRequest))->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create())
            ->post(route('account.requests.payment.store', $serviceRequest))->assertRedirect(route('dashboard'));

        Http::assertNothingSent();
        $this->assertDatabaseCount('payments', 0);
    }

    #[TestWith([ServiceRequest::STATUS_PENDING, 50])]
    #[TestWith([ServiceRequest::STATUS_COMPLETED, 50])]
    #[TestWith([ServiceRequest::STATUS_DECLINED, 50])]
    #[TestWith([ServiceRequest::STATUS_AWAITING_PAYMENT, 0])]
    public function test_unapproved_completed_declined_or_free_requests_cannot_start_checkout(string $status, int $fee): void
    {
        [$user, $serviceRequest] = $this->awaitingPaymentRequest();
        $serviceRequest->update(['status' => $status, 'fee_amount' => $fee]);

        $this->actingAs($user)->post(route('account.requests.payment.store', $serviceRequest))->assertUnprocessable();

        $this->assertDatabaseCount('payments', 0);
        Http::assertNothingSent();
    }

    #[TestWith([false])]
    #[TestWith([true])]
    public function test_verified_payment_webhooks_complete_the_request_once_without_a_signed_in_user(bool $legacyEnvelope): void
    {
        $this->freezeTime();
        $payment = Payment::factory()->paymongo()->create();
        $session = $this->checkoutSession($payment, paid: true);
        $payload = $legacyEnvelope
            ? ['data' => ['id' => 'evt_fixture', 'type' => 'event', 'attributes' => [
                'type' => 'checkout_session.payment.paid', 'livemode' => true, 'data' => $session,
            ]]]
            : ['event_type' => 'send.webhook', 'data' => [
                'type' => 'checkout_session.payment.paid', 'livemode' => true, 'data' => $session,
            ]];
        $body = json_encode($payload, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);

        $this->sendWebhook($body)->assertOk()->assertJsonPath('received', true);
        $this->sendWebhook($body)->assertOk()->assertJsonPath('received', true);

        $payment->refresh();
        $this->assertSame(Payment::STATUS_PAID, $payment->status);
        $this->assertSame('pay_fixture', $payment->provider_payment_id);
        $this->assertNotNull($payment->paid_at);
        $this->assertNull($payment->recorded_by);
        $certificate = Certificate::query()->sole();
        $this->assertSame('50.00', $certificate->fee);
        $this->assertSame(ServiceRequest::STATUS_COMPLETED, $payment->serviceRequest->status);
        $this->assertSame($certificate->id, $payment->serviceRequest->certificate_id);
        Http::assertNothingSent();
    }

    #[TestWith(['missing'])]
    #[TestWith(['forged'])]
    #[TestWith(['stale'])]
    #[TestWith(['wrong_signature_mode'])]
    #[TestWith(['tampered_body'])]
    public function test_unverified_notifications_return_401_and_do_not_mark_paid(string $kind): void
    {
        $this->freezeTime();
        $payment = Payment::factory()->paymongo()->create();
        $body = $this->webhookBody($this->checkoutSession($payment, paid: true));
        $timestamp = $kind === 'stale' ? now()->subMinutes(6)->timestamp : now()->timestamp;
        $signature = hash_hmac('sha256', $timestamp.'.'.$body, 'whsec_fixture');
        $header = $kind === 'wrong_signature_mode' ? "t={$timestamp},te={$signature},li=" : "t={$timestamp},te=,li={$signature}";
        if ($kind === 'forged') {
            $header = "t={$timestamp},te=,li=".str_repeat('0', 64);
        }
        if ($kind === 'missing') {
            $header = '';
        }
        if ($kind === 'tampered_body') {
            $body .= ' ';
        }

        $this->call('POST', route('payments.webhook.paymongo'), [], [], [], [
            'CONTENT_TYPE' => 'application/json', 'HTTP_PAYMONGO_SIGNATURE' => $header,
        ], $body)->assertUnauthorized();

        $this->assertSame(Payment::STATUS_PENDING, $payment->fresh()->status);
        $this->assertDatabaseCount('certificates', 0);
    }

    #[TestWith(['attributes.reference_number', 'another-request'])]
    #[TestWith(['attributes.metadata.payment_id', 'another-payment'])]
    #[TestWith(['attributes.metadata.service_request_id', '999'])]
    #[TestWith(['attributes.livemode', false])]
    #[TestWith(['attributes.payments.0.attributes.amount', 1])]
    #[TestWith(['attributes.payments.0.attributes.currency', 'USD'])]
    #[TestWith(['attributes.payments.0.attributes.source.type', 'gcash'])]
    #[TestWith(['attributes.payments.0.attributes.livemode', false])]
    public function test_signed_notifications_with_mismatched_payment_details_return_422_without_issuing_a_certificate(string $field, mixed $value): void
    {
        $payment = Payment::factory()->paymongo()->create();
        $session = $this->checkoutSession($payment, paid: true);
        data_set($session, $field, $value);

        $this->sendWebhook($this->webhookBody($session))->assertUnprocessable();

        $this->assertSame(Payment::STATUS_PENDING, $payment->fresh()->status);
        $this->assertDatabaseCount('certificates', 0);
    }

    #[TestWith([Payment::STATUS_EXPIRED])]
    #[TestWith([Payment::STATUS_CANCELLED])]
    public function test_delayed_verified_payments_are_recorded_even_after_local_expiry_or_cancellation(string $status): void
    {
        $payment = Payment::factory()->paymongo()->create(['status' => $status]);

        $this->sendWebhook($this->webhookBody($this->checkoutSession($payment, paid: true)))->assertOk()->assertJsonPath('received', true);

        $this->assertSame(Payment::STATUS_PAID, $payment->fresh()->status);
        $this->assertSame(ServiceRequest::STATUS_COMPLETED, $payment->serviceRequest->fresh()->status);
        $this->assertDatabaseCount('certificates', 1);
    }

    public function test_unrelated_webhooks_and_unpaid_checkouts_do_not_complete_a_request(): void
    {
        $payment = Payment::factory()->paymongo()->create();
        $unknownSession = $this->checkoutSession($payment, paid: true);
        $unknownSession['id'] = 'cs_unrelated';

        $this->sendWebhook($this->webhookBody($unknownSession))->assertOk()->assertJsonPath('received', true);
        $this->sendWebhook($this->webhookBody($this->checkoutSession($payment)))->assertOk()->assertJsonPath('received', true);

        $this->assertSame(Payment::STATUS_PENDING, $payment->fresh()->status);
        $this->assertDatabaseCount('certificates', 0);
    }

    public function test_checkout_return_parameters_cannot_mark_an_unpaid_transaction_paid(): void
    {
        [$user, $serviceRequest] = $this->awaitingPaymentRequest();
        $payment = Payment::factory()->paymongo()->for($serviceRequest)->create();
        Http::fake([$this->checkoutEndpoint($payment) => Http::response(['data' => $this->checkoutSession($payment)])]);

        $this->actingAs($user)->get(route('account.payments.return', $payment).'?status=paid&success=true')
            ->assertRedirect(route('account.requests.show', $serviceRequest))->assertSessionHas('warning');

        $this->assertSame(Payment::STATUS_PENDING, $payment->fresh()->status);
        $this->assertDatabaseCount('certificates', 0);
        Http::assertSentCount(1);
    }

    public function test_status_check_recovers_a_confirmed_payment_when_the_webhook_is_delayed(): void
    {
        [$user, $serviceRequest] = $this->awaitingPaymentRequest();
        $payment = Payment::factory()->paymongo()->for($serviceRequest)->create();
        Http::fake([$this->checkoutEndpoint($payment) => Http::response(['data' => $this->checkoutSession($payment, paid: true)])]);

        $this->actingAs($user)->getJson(route('account.payments.status', $payment))
            ->assertOk()->assertJsonPath('status', Payment::STATUS_PAID)
            ->assertJsonPath('redirect_url', route('account.requests.show', $serviceRequest, false));

        $this->assertSame(ServiceRequest::STATUS_COMPLETED, $serviceRequest->fresh()->status);
        $this->assertDatabaseCount('certificates', 1);
        Http::assertSentCount(1);
    }

    public function test_repeated_status_checks_do_not_request_paymongo_more_than_once_in_thirty_seconds(): void
    {
        $this->freezeTime();
        [$user, $serviceRequest] = $this->awaitingPaymentRequest();
        $payment = Payment::factory()->paymongo()->for($serviceRequest)->create();
        Http::fake([$this->checkoutEndpoint($payment) => Http::response(['data' => $this->checkoutSession($payment)])]);

        $this->actingAs($user)->getJson(route('account.payments.status', $payment))->assertJsonPath('status', Payment::STATUS_PENDING);
        $this->getJson(route('account.payments.status', $payment))->assertJsonPath('status', Payment::STATUS_PENDING);

        Http::assertSentCount(1);
        $this->assertDatabaseCount('certificates', 0);
    }

    public function test_failed_status_check_keeps_payment_pending_and_reports_delayed_confirmation(): void
    {
        [$user, $serviceRequest] = $this->awaitingPaymentRequest();
        $payment = Payment::factory()->paymongo()->for($serviceRequest)->create();
        Http::fake([$this->checkoutEndpoint($payment) => Http::response([], 503)]);

        $this->actingAs($user)->getJson(route('account.payments.status', $payment))
            ->assertOk()->assertJsonPath('status', Payment::STATUS_PENDING)->assertJsonPath('checking_available', false);

        $this->assertDatabaseCount('certificates', 0);
        Http::assertSentCount(1);
    }

    public function test_online_checkout_is_closed_at_paymongo_before_switching_to_cash(): void
    {
        [$user, $serviceRequest] = $this->awaitingPaymentRequest();
        $payment = Payment::factory()->paymongo()->for($serviceRequest)->create();
        Http::fake([
            $this->checkoutEndpoint($payment) => Http::sequence()
                ->push(['data' => $this->checkoutSession($payment)])
                ->push(['data' => $this->checkoutSession($payment, status: 'expired')]),
            $this->checkoutEndpoint($payment).'/expire' => Http::response(['data' => $this->checkoutSession($payment, status: 'expired')]),
        ]);

        $this->actingAs($user)->post(route('account.requests.cash-payment.store', $serviceRequest))
            ->assertRedirect(route('account.requests.show', $serviceRequest));

        $this->assertSame(Payment::STATUS_EXPIRED, $payment->fresh()->status);
        $this->assertSame(Payment::PROVIDER_CASH, $serviceRequest->latestPayment->provider);
        $this->assertDatabaseCount('payments', 2);
        Http::assertSentCount(3);
    }

    public function test_payment_confirmed_while_switching_to_cash_does_not_create_another_payment(): void
    {
        [$user, $serviceRequest] = $this->awaitingPaymentRequest();
        $payment = Payment::factory()->paymongo()->for($serviceRequest)->create();
        Http::fake([$this->checkoutEndpoint($payment) => Http::response(['data' => $this->checkoutSession($payment, paid: true)])]);

        $this->actingAs($user)->post(route('account.requests.cash-payment.store', $serviceRequest))
            ->assertRedirect(route('account.requests.show', $serviceRequest));

        $this->assertSame(Payment::STATUS_PAID, $payment->fresh()->status);
        $this->assertDatabaseCount('payments', 1);
        $this->assertDatabaseCount('certificates', 1);
        Http::assertSentCount(1);
    }

    public function test_a_checkout_that_cannot_be_closed_cannot_switch_to_cash(): void
    {
        [$user, $serviceRequest] = $this->awaitingPaymentRequest();
        $payment = Payment::factory()->paymongo()->for($serviceRequest)->create();
        Http::fake([
            $this->checkoutEndpoint($payment) => Http::response(['data' => $this->checkoutSession($payment)]),
            $this->checkoutEndpoint($payment).'/expire' => Http::response([], 503),
        ]);

        $this->actingAs($user)->post(route('account.requests.cash-payment.store', $serviceRequest))
            ->assertSessionHasErrors('payment');

        $this->assertSame(Payment::STATUS_PENDING, $payment->fresh()->status);
        $this->assertDatabaseCount('payments', 1);
        Http::assertSentCount(2);
    }

    public function test_expired_checkout_is_closed_before_a_new_checkout_is_created(): void
    {
        $this->freezeTime();
        [$user, $serviceRequest] = $this->awaitingPaymentRequest();
        $payment = Payment::factory()->paymongo()->for($serviceRequest)->create(['expires_at' => now()->subMinute()]);
        Http::fake([
            $this->checkoutEndpoint($payment) => Http::sequence()
                ->push(['data' => $this->checkoutSession($payment)])
                ->push(['data' => $this->checkoutSession($payment)])
                ->push(['data' => $this->checkoutSession($payment, status: 'expired')]),
            $this->checkoutEndpoint($payment).'/expire' => Http::response(['data' => $this->checkoutSession($payment, status: 'expired')]),
        ]);
        $this->fakeCreateCheckout();

        $this->actingAs($user)->post(route('account.requests.payment.store', $serviceRequest))
            ->assertRedirect('https://checkout.paymongo.com/cs_fixture');

        $this->assertSame(Payment::STATUS_EXPIRED, $payment->fresh()->status);
        $this->assertDatabaseCount('payments', 2);
        Http::assertSentCount(5);
    }

    public function test_resident_can_choose_cash_and_switch_to_qr_ph_before_payment(): void
    {
        [$user, $serviceRequest] = $this->awaitingPaymentRequest();
        $this->fakeCreateCheckout();

        $this->actingAs($user)->post(route('account.requests.cash-payment.store', $serviceRequest))
            ->assertRedirect(route('account.requests.show', $serviceRequest));
        $cashPayment = Payment::query()->sole();
        $this->get(route('account.requests.show', $serviceRequest))->assertSeeText('CASH PAYMENT')->assertSeeText('Switch to QR Ph');
        $this->post(route('account.requests.payment.store', $serviceRequest))->assertRedirect();

        $this->assertSame(Payment::STATUS_CANCELLED, $cashPayment->fresh()->status);
        $this->assertSame(Payment::PROVIDER_PAYMONGO_QRPH, $serviceRequest->fresh()->latestPayment->provider);
        Http::assertSentCount(1);
    }

    public function test_staff_records_cash_payment_and_issues_only_one_certificate(): void
    {
        [$user, $serviceRequest] = $this->awaitingPaymentRequest();
        $this->actingAs($user)->post(route('account.requests.cash-payment.store', $serviceRequest));
        $payment = Payment::query()->sole();
        $staff = User::factory()->create();

        $this->actingAs($staff)->get(route('service-requests.show', $serviceRequest))->assertSeeText('Record cash payment');
        $this->post(route('payments.cash.confirm', $payment), [])->assertSessionHasErrors('receipt_number');
        $this->post(route('payments.cash.confirm', $payment), ['receipt_number' => 'OR-2026-00125'])
            ->assertRedirect(route('service-requests.show', $serviceRequest));
        $this->post(route('payments.cash.confirm', $payment), ['receipt_number' => 'OR-2026-00125'])->assertRedirect();

        $this->assertSame(Payment::STATUS_PAID, $payment->fresh()->status);
        $this->assertSame('OR-2026-00125', $payment->fresh()->receipt_number);
        $this->assertSame($staff->id, $payment->fresh()->recorded_by);
        $this->assertSame(ServiceRequest::STATUS_COMPLETED, $serviceRequest->fresh()->status);
        $this->assertDatabaseCount('certificates', 1);
        Http::assertNothingSent();
    }

    public function test_staff_cannot_use_cash_confirmation_to_mark_an_online_payment_paid(): void
    {
        $payment = Payment::factory()->paymongo()->create();

        $this->actingAs(User::factory()->create())->post(route('payments.cash.confirm', $payment), [
            'receipt_number' => 'OR-2026-00125',
        ])->assertNotFound();

        $this->assertSame(Payment::STATUS_PENDING, $payment->fresh()->status);
        $this->assertDatabaseCount('certificates', 0);
    }

    public function test_old_signed_confirmation_urls_cannot_mark_a_payment_paid(): void
    {
        $payment = Payment::factory()->create();
        $signedUrl = URL::signedRoute('home').'&payment='.$payment->public_id;

        $this->get('/demo-payments/'.$payment->public_id)->assertNotFound();
        $this->post('/demo-payments/'.$payment->public_id.'/confirm?'.parse_url($signedUrl, PHP_URL_QUERY))->assertNotFound();

        $this->assertSame(Payment::STATUS_PENDING, $payment->fresh()->status);
        $this->assertDatabaseCount('certificates', 0);
    }

    public function test_verified_webhook_does_not_require_a_browser_csrf_token(): void
    {
        $payment = Payment::factory()->paymongo()->create();
        $this->app->instance('env', 'production');

        $this->sendWebhook($this->webhookBody($this->checkoutSession($payment, paid: true)))->assertOk()->assertJsonPath('received', true);

        $this->assertSame(Payment::STATUS_PAID, $payment->fresh()->status);
        $this->assertDatabaseCount('certificates', 1);
    }

    public function test_signed_malformed_json_returns_400_without_changing_payment_records(): void
    {
        $payment = Payment::factory()->paymongo()->create();

        $this->sendWebhook('{')->assertBadRequest();

        $this->assertSame(Payment::STATUS_PENDING, $payment->fresh()->status);
        $this->assertDatabaseCount('certificates', 0);
    }

    public function test_payment_reconciliation_completes_a_paid_request_without_a_browser(): void
    {
        $payment = Payment::factory()->paymongo()->create();
        Http::fake([$this->checkoutEndpoint($payment) => Http::response(['data' => $this->checkoutSession($payment, paid: true)])]);

        $this->artisan('payments:sync')->expectsOutput('1 payment record(s) checked; 0 could not be confirmed.')->assertSuccessful();

        $this->assertSame(Payment::STATUS_PAID, $payment->fresh()->status);
        $this->assertSame(ServiceRequest::STATUS_COMPLETED, $payment->serviceRequest->fresh()->status);
        $this->assertDatabaseCount('certificates', 1);
        Http::assertSentCount(1);
    }

    public function test_one_provider_failure_does_not_stop_other_payments_from_being_reconciled(): void
    {
        $first = Payment::factory()->paymongo()->create();
        $second = Payment::factory()->paymongo()->create();
        Http::fake([
            $this->checkoutEndpoint($first) => Http::response([], 503),
            $this->checkoutEndpoint($second) => Http::response(['data' => $this->checkoutSession($second, paid: true)]),
        ]);

        $this->artisan('payments:sync')->expectsOutput('2 payment record(s) checked; 1 could not be confirmed.')->assertFailed();

        $this->assertSame(Payment::STATUS_PENDING, $first->fresh()->status);
        $this->assertSame(Payment::STATUS_PAID, $second->fresh()->status);
        $this->assertDatabaseCount('certificates', 1);
        Http::assertSentCount(2);
    }

    public function test_reconciliation_skips_cash_paid_and_other_mode_payments(): void
    {
        Payment::factory()->cash()->create();
        Payment::factory()->paymongo()->paid()->create();
        Payment::factory()->paymongo()->create(['provider_livemode' => false]);

        $this->artisan('payments:sync')->expectsOutput('0 payment record(s) checked; 0 could not be confirmed.')->assertSuccessful();

        Http::assertNothingSent();
    }

    public function test_an_api_response_for_another_checkout_cannot_update_either_request(): void
    {
        [$user, $serviceRequest] = $this->awaitingPaymentRequest();
        $payment = Payment::factory()->paymongo()->for($serviceRequest)->create();
        $another = Payment::factory()->paymongo()->create();
        Http::fake([$this->checkoutEndpoint($payment) => Http::response(['data' => $this->checkoutSession($another, paid: true)])]);

        $this->actingAs($user)->getJson(route('account.payments.status', $payment))->assertUnprocessable();

        $this->assertSame(Payment::STATUS_PENDING, $payment->fresh()->status);
        $this->assertSame(Payment::STATUS_PENDING, $another->fresh()->status);
        $this->assertDatabaseCount('certificates', 0);
        Http::assertSentCount(1);
    }

    /** @return array{User, ServiceRequest} */
    private function awaitingPaymentRequest(): array
    {
        $resident = Resident::factory()->create();
        $user = User::factory()->resident()->create(['resident_id' => $resident->id]);
        $serviceRequest = ServiceRequest::factory()->for($resident)->create([
            'status' => ServiceRequest::STATUS_AWAITING_PAYMENT,
            'fee_amount' => 50,
            'reviewed_at' => now(),
        ]);

        return [$user, $serviceRequest];
    }

    private function fakeCreateCheckout(): void
    {
        Http::fake(['https://api.paymongo.com/v2/checkout_sessions' => fn (Request $request) => Http::response([
            'data' => ['id' => 'cs_fixture', 'type' => 'checkout_session', 'attributes' => [
                'checkout_url' => 'https://checkout.paymongo.com/cs_fixture', 'livemode' => true,
            ]],
        ])]);
    }

    private function checkoutEndpoint(Payment $payment): string
    {
        return 'https://api.paymongo.com/v1/checkout_sessions/'.$payment->provider_reference;
    }

    /** @return array<string, mixed> */
    private function checkoutSession(Payment $payment, bool $paid = false, string $status = 'active'): array
    {
        return [
            'id' => $payment->provider_reference, 'type' => 'checkout_session',
            'attributes' => [
                'livemode' => true, 'status' => $status,
                'reference_number' => $payment->public_id,
                'metadata' => ['payment_id' => $payment->public_id, 'service_request_id' => (string) $payment->service_request_id],
                'payments' => $paid ? [[
                    'id' => 'pay_fixture', 'type' => 'payment',
                    'attributes' => ['status' => 'paid', 'amount' => 5000, 'currency' => 'PHP', 'livemode' => true, 'source' => ['type' => 'qrph']],
                ]] : [],
            ],
        ];
    }

    /** @param array<string, mixed> $session */
    private function webhookBody(array $session): string
    {
        return json_encode(['event_type' => 'send.webhook', 'data' => [
            'type' => 'checkout_session.payment.paid', 'livemode' => true, 'data' => $session,
        ]], JSON_THROW_ON_ERROR);
    }

    private function sendWebhook(string $body): TestResponse
    {
        $timestamp = now()->timestamp;
        $signature = hash_hmac('sha256', $timestamp.'.'.$body, 'whsec_fixture');

        return $this->call('POST', route('payments.webhook.paymongo'), [], [], [], [
            'CONTENT_TYPE' => 'application/json', 'HTTP_PAYMONGO_SIGNATURE' => "t={$timestamp},te=,li={$signature}",
        ], $body);
    }
}
