@extends('layouts.app')

@section('title', 'Resident Request Details | Barangay Information System')

@section('breadcrumb')
    <a href="{{ route('service-requests.index', ['status' => $serviceRequest->status]) }}">Resident requests</a><i data-lucide="chevron-right" aria-hidden="true"></i><strong>Request #{{ $serviceRequest->id }}</strong>
@endsection

@section('content')
    <div class="staff-requests-page staff-request-detail">
        <x-workspace.page-header :title="$serviceRequest->type === 'certificate' ? $serviceRequest->certificate_type : 'Blotter report'" :description="'Request #'.str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT).' · Submitted '.$serviceRequest->created_at->timezone('Asia/Manila')->format('M j, Y \a\t g:i A')" :icon="$serviceRequest->type === 'certificate' ? 'files' : 'notebook-pen'">
            <x-slot:status><x-request-status :status="$serviceRequest->status" /></x-slot:status>
        </x-workspace.page-header>

        <div class="staff-request-detail-grid">
            <section class="staff-request-detail-card" aria-labelledby="staff-request-details-title">
                <div class="staff-request-card-heading">
                    <span class="staff-request-card-icon"><i data-lucide="clipboard-list" aria-hidden="true"></i></span>
                    <div>
                        <h2 id="staff-request-details-title">Request details</h2>
                        <p>Information submitted by the resident</p>
                    </div>
                </div>
                <dl class="staff-request-fields">
                    <div>
                        <dt>{{ $serviceRequest->type === 'blotter' ? 'Complainant' : 'Resident' }}</dt>
                        <dd>
                            @if ($serviceRequest->resident)
                                <a href="{{ route('residents.show', $serviceRequest->resident) }}">{{ $serviceRequest->resident->full_name }}</a>
                            @else
                                Resident record unavailable
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt>Address</dt>
                        <dd>{{ $serviceRequest->resident?->address ?? 'Not available' }}</dd>
                    </div>
                    @if ($serviceRequest->type === 'certificate')
                        <div>
                            <dt>Document requested</dt>
                            <dd>{{ $serviceRequest->certificate_type }}</dd>
                        </div>
                        <div>
                            <dt>Fixed document fee</dt>
                            <dd>{{ (float) $certificateFee === 0.0 ? 'Free' : '₱'.number_format((float) $certificateFee, 2) }}</dd>
                        </div>
                        <div class="staff-request-field-long">
                            <dt>Purpose</dt>
                            <dd>{{ $serviceRequest->purpose }}</dd>
                        </div>
                    @else
                        <div>
                            <dt>Respondent</dt>
                            <dd>{{ $serviceRequest->respondent }}</dd>
                        </div>
                        <div>
                            <dt>Incident date</dt>
                            <dd><time datetime="{{ $serviceRequest->incident_date->toDateString() }}">{{ $serviceRequest->incident_date->format('M j, Y') }}</time></dd>
                        </div>
                        <div class="staff-request-field-long">
                            <dt>Incident details</dt>
                            <dd>{{ $serviceRequest->incident }}</dd>
                        </div>
                    @endif
                </dl>
            </section>

            <section class="staff-request-detail-card staff-request-review-card" aria-labelledby="staff-request-review-title">
                <div class="staff-request-card-heading">
                    <span class="staff-request-card-icon"><i data-lucide="{{ $serviceRequest->status === 'Pending' ? 'clock' : ($serviceRequest->status === 'Awaiting Payment' ? 'qr-code' : 'badge-check') }}" aria-hidden="true"></i></span>
                    <div>
                        <h2 id="staff-request-review-title">{{ $serviceRequest->status === 'Pending' ? 'Verify request' : 'Verification outcome' }}</h2>
                        @if ($serviceRequest->status === 'Pending')
                            <p>Approve or decline this submission</p>
                        @else
                            <p>Decision recorded by barangay staff</p>
                        @endif
                    </div>
                </div>

                @if ($serviceRequest->status === 'Pending')
                    <p class="staff-request-review-help">{{ $serviceRequest->type === 'certificate' ? 'Verify the request details. The fixed fee is applied automatically; free documents are issued immediately after approval.' : 'Completing this request creates an official blotter record.' }} If you decline it, explain why in the message below.</p>
                    <form method="POST" action="{{ route('service-requests.review', $serviceRequest) }}" class="staff-request-review-form">
                        @csrf
                        <div class="staff-request-review-field">
                            <x-form.label for="response_note">Message to resident</x-form.label>
                            <x-form.textarea name="response_note" rows="4" maxlength="1000" placeholder="Add collection instructions or explain why the request was declined" :aria-describedby="$errors->has('response_note') ? 'response_note_help response_note-error' : 'response_note_help'" :aria-invalid="$errors->has('response_note') ? 'true' : null">{{ old('response_note') }}</x-form.textarea>
                            <p id="response_note_help">Optional when completing; required when declining.</p>
                            <x-form.error :message="$errors->first('response_note')" :id="'response_note-error'" />
                        </div>
                        <div class="staff-request-review-actions">
                            <button type="submit" name="decision" value="complete" class="button button-primary"><i data-lucide="check" aria-hidden="true"></i> {{ $serviceRequest->type === 'certificate' ? 'Approve request' : 'Complete request' }}</button>
                            <button type="submit" name="decision" value="decline" class="button button-danger-outline">Decline request</button>
                        </div>
                        <x-form.error :message="$errors->first('decision')" />
                    </form>
                @else
                    <dl class="staff-request-outcome">
                        <div>
                            <dt>Reviewed by</dt>
                            <dd>{{ $serviceRequest->reviewer?->name ?? 'Staff account unavailable' }}</dd>
                        </div>
                        @if ($serviceRequest->reviewed_at)
                            <div>
                                <dt>Reviewed on</dt>
                                <dd><time datetime="{{ $serviceRequest->reviewed_at->toIso8601String() }}">{{ $serviceRequest->reviewed_at->timezone('Asia/Manila')->format('M j, Y \a\t g:i A') }}</time></dd>
                            </div>
                        @endif
                        @if ($serviceRequest->type === 'certificate' && $serviceRequest->fee_amount !== null)
                            <div>
                                <dt>Document fee</dt>
                                <dd>₱{{ number_format((float) $serviceRequest->fee_amount, 2) }}</dd>
                            </div>
                            <div>
                                <dt>Payment method</dt>
                                <dd>{{ $serviceRequest->latestPayment ? ($serviceRequest->latestPayment->provider === \App\Models\Payment::PROVIDER_CASH ? 'Cash' : 'QRPH') : ($serviceRequest->status === 'Awaiting Payment' ? 'Not selected yet' : 'Not required') }}</dd>
                            </div>
                            <div>
                                <dt>Payment status</dt>
                                <dd>{{ $serviceRequest->latestPayment ? ucfirst($serviceRequest->latestPayment->status) : ($serviceRequest->status === 'Awaiting Payment' ? 'Waiting for resident' : 'Not required') }}</dd>
                            </div>
                            @if ($serviceRequest->latestPayment?->receipt_number)
                                <div><dt>Receipt number</dt><dd>{{ $serviceRequest->latestPayment->receipt_number }}</dd></div>
                            @endif
                            @if ($serviceRequest->latestPayment?->paid_at)
                                <div><dt>Paid on</dt><dd>{{ $serviceRequest->latestPayment->paid_at->timezone('Asia/Manila')->format('M j, Y \a\t g:i A') }}</dd></div>
                            @endif
                        @endif
                        <div class="staff-request-field-long">
                            <dt>Message to resident</dt>
                            <dd>{{ $serviceRequest->response_note ?: 'No message was added.' }}</dd>
                        </div>
                    </dl>
                    @if ($serviceRequest->latestPayment?->provider === \App\Models\Payment::PROVIDER_CASH && $serviceRequest->latestPayment->status === \App\Models\Payment::STATUS_PENDING)
                        <div class="staff-cash-payment">
                            <div><h3>Record cash payment</h3><p>Confirm only after receiving the cash at the barangay hall.</p></div>
                            <form method="POST" action="{{ route('payments.cash.confirm', $serviceRequest->latestPayment) }}" class="staff-request-review-form">
                                @csrf
                                <div class="staff-request-review-field">
                                    <x-form.label for="receipt_number" required>Official receipt or reference number</x-form.label>
                                    <x-form.input name="receipt_number" id="receipt_number" :value="old('receipt_number')" required maxlength="100" placeholder="e.g. OR-2026-00125" :aria-invalid="$errors->has('receipt_number') ? 'true' : null" :aria-describedby="$errors->has('receipt_number') ? 'receipt_number-error' : null" />
                                    <x-form.error :message="$errors->first('receipt_number')" :id="'receipt_number-error'" />
                                </div>
                                <div class="staff-request-review-field">
                                    <x-form.label for="payment_note">Payment note</x-form.label>
                                    <x-form.textarea name="payment_note" id="payment_note" rows="3" maxlength="500" placeholder="Optional note about the cash payment" :aria-invalid="$errors->has('payment_note') ? 'true' : null" :aria-describedby="$errors->has('payment_note') ? 'payment_note-error' : null">{{ old('payment_note') }}</x-form.textarea>
                                    <x-form.error :message="$errors->first('payment_note')" :id="'payment_note-error'" />
                                </div>
                                <button type="submit" class="button button-primary"><i data-lucide="check" aria-hidden="true"></i> Record cash payment</button>
                            </form>
                        </div>
                    @endif
                    @if ($serviceRequest->certificate)
                        <a href="{{ route('certificates.show', $serviceRequest->certificate) }}" class="staff-request-record-link">View issued certificate <i data-lucide="arrow-right" aria-hidden="true"></i></a>
                    @elseif ($serviceRequest->blotter)
                        <a href="{{ route('blotters.show', $serviceRequest->blotter) }}" class="staff-request-record-link">View blotter record <i data-lucide="arrow-right" aria-hidden="true"></i></a>
                    @endif
                @endif
            </section>
        </div>
    </div>
@endsection
