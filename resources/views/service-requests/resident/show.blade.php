@extends('layouts.resident')

@section('title', 'Request Details | Barangay Information System')

@section('content')
    <div class="resident-detail-page">
        <x-workspace.page-header :title="$serviceRequest->type === 'certificate' ? $serviceRequest->certificate_type : 'Blotter report'" :description="'Request #'.str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT).' · Submitted '.$serviceRequest->created_at->timezone('Asia/Manila')->format('M j, Y')" :icon="$serviceRequest->type === 'certificate' ? 'files' : 'notebook-pen'">
            <x-slot:status><x-request-status :status="$serviceRequest->status" /></x-slot:status>
            <x-slot:actions>
                <a href="{{ route('account.requests.index') }}" class="resident-button resident-button-primary"><i data-lucide="arrow-left" aria-hidden="true"></i> My requests</a>
            </x-slot:actions>
        </x-workspace.page-header>

        <section class="resident-request-status resident-request-status-{{ \Illuminate\Support\Str::slug($serviceRequest->status) }}" aria-labelledby="request-status-title">
            <span class="resident-request-status-icon"><i data-lucide="{{ $serviceRequest->status === 'Pending' ? 'calendar-clock' : ($serviceRequest->status === 'Awaiting Payment' ? 'landmark' : ($serviceRequest->status === 'Completed' ? 'badge-check' : 'info')) }}" aria-hidden="true"></i></span>
            <div>
                @if ($serviceRequest->status === 'Completed')
                    <h2 id="request-status-title">{{ $serviceRequest->type === 'certificate' ? 'Your document has been issued' : 'Your report has been recorded' }}</h2>
                    <p>{{ $serviceRequest->type === 'certificate' ? 'Contact barangay staff about collecting your document.' : 'Your report has been added to the official barangay blotter records. Contact staff for any follow-up on the incident.' }} @if ($serviceRequest->response_note) Check their message below for any instructions. @endif</p>
                @elseif ($serviceRequest->status === 'Awaiting Payment')
                    <h2 id="request-status-title">Payment required</h2>
                    @if ($serviceRequest->latestPayment?->provider === \App\Models\Payment::PROVIDER_CASH && $serviceRequest->latestPayment->status === \App\Models\Payment::STATUS_PENDING)
                        <p>Pay cash at the barangay hall and present your request number. Staff will confirm your payment.</p>
                    @elseif ($serviceRequest->latestPayment?->provider === \App\Models\Payment::PROVIDER_PAYMONGO_QRPH && $serviceRequest->latestPayment->status === \App\Models\Payment::STATUS_PENDING)
                        <p>Pay through QR Ph using GCash, Maya, or your banking app. Confirmation is automatic.</p>
                    @else
                        <p>Choose QR Ph or cash at the barangay hall below to complete your payment.</p>
                    @endif
                @elseif ($serviceRequest->status === 'Declined')
                    <h2 id="request-status-title">This request was declined</h2>
                    <p>{{ $serviceRequest->response_note ? 'Read the staff message below for feedback. Contact the barangay office if you need clarification.' : 'Contact barangay staff for the reason and guidance on what to do next.' }}</p>
                @else
                    <h2 id="request-status-title">Waiting for verification</h2>
                    <p>Your request has been received. Barangay staff will verify the details, and the result will appear on this page.</p>
                @endif
            </div>
            @if ($serviceRequest->response_note)
                <a href="#staff-message" class="resident-inline-link">Read staff message <i data-lucide="arrow-right" aria-hidden="true"></i></a>
            @endif
        </section>

        <div class="resident-request-layout">
            <div class="resident-request-content">
                <section class="resident-card resident-detail-card" aria-labelledby="request-details-title">
                    <div class="resident-detail-heading">
                        <span class="resident-section-icon"><i data-lucide="clipboard-list" aria-hidden="true"></i></span>
                        <div><h2 id="request-details-title">Request details</h2></div>
                    </div>
                    <dl class="resident-detail-grid">
                        @if ($serviceRequest->type === 'certificate')
                            <div><dt>Document requested</dt><dd>{{ $serviceRequest->certificate_type }}</dd></div>
                            @if ($serviceRequest->fee_amount !== null)
                                <div><dt>Document fee</dt><dd>₱{{ number_format((float) $serviceRequest->fee_amount, 2) }}</dd></div>
                            @endif
                            <div class="resident-detail-description"><dt>Purpose of request</dt><dd>{{ $serviceRequest->purpose }}</dd></div>
                        @else
                            <div><dt>Respondent</dt><dd>{{ $serviceRequest->respondent }}</dd></div>
                            <div><dt>Incident date</dt><dd>{{ $serviceRequest->incident_date->format('M j, Y') }}</dd></div>
                            <div class="resident-detail-description"><dt>Incident details</dt><dd>{{ $serviceRequest->incident }}</dd></div>
                        @endif
                    </dl>
                </section>

                @if ($serviceRequest->type === \App\Models\ServiceRequest::TYPE_CERTIFICATE && $serviceRequest->status === \App\Models\ServiceRequest::STATUS_AWAITING_PAYMENT)
                    <x-resident-payment :service-request="$serviceRequest" :online-payment-available="$onlinePaymentAvailable" />
                @endif

                @if ($serviceRequest->type === \App\Models\ServiceRequest::TYPE_BLOTTER && $serviceRequest->blotter)
                    <section class="resident-card incident-timeline-card" aria-labelledby="blotter-progress-title">
                        <div class="resident-detail-heading"><span class="resident-section-icon"><i data-lucide="history" aria-hidden="true"></i></span><div><h2 id="blotter-progress-title">Blotter case progress</h2><p>Current status: <x-blotter-status :status="$serviceRequest->blotter->status" /></p></div></div>
                        @if ($serviceRequest->blotter->hearing_at)
                            <p class="incident-timeline-footnote">Mediation schedule (Philippine time): <strong>{{ $serviceRequest->blotter->hearing_at->timezone('Asia/Manila')->format('M j, Y \a\t g:i A') }}</strong></p>
                        @endif
                        <ol class="incident-timeline">
                            <li><span class="incident-timeline-dot" aria-hidden="true"></span><div><x-blotter-status :status="\App\Models\Blotter::STATUS_PENDING" /><time datetime="{{ $serviceRequest->blotter->created_at->toIso8601String() }}">{{ $serviceRequest->blotter->created_at->timezone('Asia/Manila')->format('M j, Y \a\t g:i A') }}</time><p>Your report was added to the official barangay blotter.</p></div></li>
                            @foreach ($serviceRequest->blotter->updates as $update)
                                <li><span class="incident-timeline-dot" aria-hidden="true"></span><div><x-blotter-status :status="$update->status" /><time datetime="{{ $update->created_at->toIso8601String() }}">{{ $update->created_at->timezone('Asia/Manila')->format('M j, Y \a\t g:i A') }}</time><p>{{ $update->message }}</p></div></li>
                            @endforeach
                        </ol>
                    </section>
                @endif

                @if ($serviceRequest->status !== 'Pending' || $serviceRequest->response_note)
                    <section id="staff-message" class="resident-card resident-staff-message" aria-labelledby="staff-message-title" tabindex="-1">
                        <div class="resident-detail-heading"><span class="resident-section-icon"><i data-lucide="messages-square" aria-hidden="true"></i></span><div><h2 id="staff-message-title">Message from barangay staff</h2>@if ($serviceRequest->response_note && $serviceRequest->reviewed_at)<p><time datetime="{{ $serviceRequest->reviewed_at->toIso8601String() }}">{{ $serviceRequest->reviewed_at->timezone('Asia/Manila')->format('M j, Y \a\t g:i A') }}</time></p>@endif</div></div>
                        @if ($serviceRequest->response_note)
                            <p class="resident-staff-message-text">{{ $serviceRequest->response_note }}</p>
                        @else
                            <div class="resident-staff-message-empty"><strong>No additional message</strong><p>Staff did not leave a message with this decision. You can contact the barangay office if you have questions.</p></div>
                        @endif
                    </section>
                @endif
            </div>

            <aside class="resident-request-sidebar" aria-label="Request progress and follow-up">
                <section class="resident-card resident-progress-card" aria-labelledby="request-progress-title">
                    <div class="resident-progress-heading"><h2 id="request-progress-title">Request progress</h2></div>
                    <ol class="resident-progress" aria-label="Request progress">
                        <li class="resident-progress-done"><span class="resident-progress-number"><i data-lucide="check" aria-hidden="true"></i></span><div><strong>Request submitted</strong><time datetime="{{ $serviceRequest->created_at->toIso8601String() }}">{{ $serviceRequest->created_at->timezone('Asia/Manila')->format('M j, Y \a\t g:i A') }}</time></div></li>
                        <li class="{{ $serviceRequest->status === 'Pending' ? 'resident-progress-current' : ($serviceRequest->status === 'Declined' ? 'resident-progress-declined' : 'resident-progress-done') }}" @if (in_array($serviceRequest->status, ['Pending', 'Declined'], true)) aria-current="step" @endif>
                            <span class="resident-progress-number"><i data-lucide="{{ $serviceRequest->status === 'Pending' ? 'calendar-clock' : ($serviceRequest->status === 'Declined' ? 'x' : 'check') }}" aria-hidden="true"></i></span>
                            <div><strong>{{ $serviceRequest->status === 'Pending' ? 'Request verification' : ($serviceRequest->status === 'Declined' ? 'Request declined' : 'Request approved') }}</strong>@if ($serviceRequest->status === 'Pending')<span class="resident-progress-label">Current step</span>@elseif ($serviceRequest->reviewed_at)<time datetime="{{ $serviceRequest->reviewed_at->toIso8601String() }}">{{ $serviceRequest->reviewed_at->timezone('Asia/Manila')->format('M j, Y \a\t g:i A') }}</time>@endif</div>
                        </li>
                        @if ($serviceRequest->status !== \App\Models\ServiceRequest::STATUS_DECLINED && $serviceRequest->type === \App\Models\ServiceRequest::TYPE_CERTIFICATE)
                            <li class="{{ $serviceRequest->status === 'Awaiting Payment' ? 'resident-progress-current' : ($serviceRequest->status === 'Completed' ? 'resident-progress-done' : 'resident-progress-upcoming') }}" @if ($serviceRequest->status === 'Awaiting Payment') aria-current="step" @endif>
                                <span class="resident-progress-number">@if ($serviceRequest->status === 'Completed')<i data-lucide="check" aria-hidden="true"></i>@elseif ($serviceRequest->status === 'Awaiting Payment')<i data-lucide="{{ $serviceRequest->latestPayment?->provider === \App\Models\Payment::PROVIDER_CASH ? 'landmark' : 'qr-code' }}" aria-hidden="true"></i>@else 3 @endif</span>
                                <div><strong>{{ (float) $serviceRequest->fee_amount === 0.0 ? 'Payment check' : ($serviceRequest->latestPayment?->provider === \App\Models\Payment::PROVIDER_CASH ? 'Cash payment' : ($serviceRequest->latestPayment?->provider === \App\Models\Payment::PROVIDER_PAYMONGO_QRPH ? 'QR Ph payment' : 'Choose payment method')) }}</strong>@if ($serviceRequest->status === 'Awaiting Payment')<span class="resident-progress-label">Current step</span>@endif</div>
                            </li>
                            <li class="{{ $serviceRequest->status === 'Completed' ? 'resident-progress-done' : 'resident-progress-upcoming' }}" @if ($serviceRequest->status === 'Completed') aria-current="step" @endif><span class="resident-progress-number">@if ($serviceRequest->status === 'Completed')<i data-lucide="check" aria-hidden="true"></i>@else 4 @endif</span><div><strong>Document issued</strong></div></li>
                        @elseif ($serviceRequest->status !== \App\Models\ServiceRequest::STATUS_DECLINED)
                            <li class="{{ $serviceRequest->status === 'Pending' ? 'resident-progress-upcoming' : ($serviceRequest->status === 'Declined' ? 'resident-progress-declined' : 'resident-progress-done') }}" @if ($serviceRequest->status !== 'Pending') aria-current="step" @endif><span class="resident-progress-number">@if ($serviceRequest->status === 'Pending') 3 @else <i data-lucide="{{ $serviceRequest->status === 'Completed' ? 'check' : 'x' }}" aria-hidden="true"></i> @endif</span><div><strong>{{ $serviceRequest->status === 'Completed' ? 'Report recorded' : ($serviceRequest->status === 'Declined' ? 'Request declined' : 'Request result') }}</strong></div></li>
                        @endif
                    </ol>
                </section>
                <section class="resident-follow-up" aria-labelledby="follow-up-title"><i data-lucide="info" aria-hidden="true"></i><div><h2 id="follow-up-title">Need to follow up?</h2><p>When contacting barangay staff, mention <strong>request #{{ str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT) }}</strong> so they can find your submission.</p></div></section>
            </aside>
        </div>
    </div>
@endsection
