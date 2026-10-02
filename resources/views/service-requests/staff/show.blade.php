@extends('layouts.app')

@section('title', 'Resident Request Details | Barangay Information System')

@section('breadcrumb')
    <a href="{{ route('service-requests.index', ['status' => $serviceRequest->status]) }}">Resident requests</a><i data-lucide="chevron-right" aria-hidden="true"></i><strong>Request #{{ $serviceRequest->id }}</strong>
@endsection

@section('content')
    <div class="staff-requests-page staff-request-detail">
        <x-workspace.page-header :title="$serviceRequest->type === 'certificate' ? $serviceRequest->certificate_type : 'Blotter report'" :description="'Request #'.str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT).' · Submitted '.$serviceRequest->created_at->format('M j, Y \a\t g:i A')" :icon="$serviceRequest->type === 'certificate' ? 'files' : 'notebook-pen'">
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
                    <span class="staff-request-card-icon"><i data-lucide="{{ $serviceRequest->status === 'Pending' ? 'clock' : 'badge-check' }}" aria-hidden="true"></i></span>
                    <div>
                        <h2 id="staff-request-review-title">{{ $serviceRequest->status === 'Pending' ? 'Review request' : 'Review outcome' }}</h2>
                        @if ($serviceRequest->status === 'Pending')
                            <p>Complete or decline this submission</p>
                        @else
                            <p>Decision recorded by barangay staff</p>
                        @endif
                    </div>
                </div>

                @if ($serviceRequest->status === 'Pending')
                    <p class="staff-request-review-help">Completing this request creates an official {{ $serviceRequest->type === 'certificate' ? 'certificate' : 'blotter' }} record. If you decline it, explain why in the message below.</p>
                    <form method="POST" action="{{ route('service-requests.review', $serviceRequest) }}" class="staff-request-review-form">
                        @csrf
                        <div class="staff-request-review-field">
                            <x-form.label for="response_note">Message to resident</x-form.label>
                            <x-form.textarea name="response_note" rows="4" maxlength="1000" placeholder="Add collection instructions or explain why the request was declined" aria-describedby="response_note_help" :aria-invalid="$errors->has('response_note') ? 'true' : null">{{ old('response_note') }}</x-form.textarea>
                            <p id="response_note_help">Optional when completing; required when declining.</p>
                            <x-form.error :message="$errors->first('response_note')" />
                        </div>
                        <div class="staff-request-review-actions">
                            <button type="submit" name="decision" value="complete" class="button button-primary"><i data-lucide="check" aria-hidden="true"></i> Complete request</button>
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
                                <dd><time datetime="{{ $serviceRequest->reviewed_at->toIso8601String() }}">{{ $serviceRequest->reviewed_at->format('M j, Y \a\t g:i A') }}</time></dd>
                            </div>
                        @endif
                        <div class="staff-request-field-long">
                            <dt>Message to resident</dt>
                            <dd>{{ $serviceRequest->response_note ?: 'No message was added.' }}</dd>
                        </div>
                    </dl>
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
