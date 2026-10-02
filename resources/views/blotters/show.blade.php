@extends('layouts.app')

@section('title')
    Blotter #{{ $blotter->id }} | Barangay Information System
@endsection

@section('main-style', 'max-width: 1180px;')

@section('breadcrumb')
    <a href="{{ route('blotters.index') }}" style="color: inherit; text-decoration: none;">Blotter Records</a>
    <i data-lucide="chevron-right"></i>
    <strong>Blotter #{{ $blotter->id }}</strong>
@endsection

@section('content')
    <div class="staff-requests-page">
        <header class="staff-request-detail-heading">
            <span class="staff-request-detail-icon"><i data-lucide="notebook-pen" aria-hidden="true"></i></span>
            <div>
                <div class="blotter-title-row">
                    <h1>Blotter #{{ $blotter->id }}</h1>
                    <x-blotter-status :status="$blotter->status" />
                </div>
                <p>Review the case details and record each step of barangay mediation.</p>
            </div>
            <a href="{{ route('blotters.edit', [$blotter]) }}" class="button button-outline" style="margin-left: auto; text-decoration: none;">
                <i data-lucide="pencil" aria-hidden="true"></i> Edit details
            </a>
        </header>

        <div class="staff-request-detail-grid incident-staff-grid">
            <div class="incident-detail-main">
                <section class="staff-request-detail-card" aria-labelledby="blotter-details-title">
                    <div class="staff-request-card-heading">
                        <span class="staff-request-card-icon"><i data-lucide="clipboard-list" aria-hidden="true"></i></span>
                        <div><h2 id="blotter-details-title">Case details</h2><p>Information recorded in the barangay blotter</p></div>
                    </div>
                    <dl class="staff-request-fields">
                        <div><dt>Complainant</dt><dd>{{ $blotter->complainant }}</dd></div>
                        <div><dt>Respondent</dt><dd>{{ $blotter->respondent }}</dd></div>
                        <div><dt>Incident date</dt><dd>{{ $blotter->incident_date?->format('M j, Y') ?? 'Not provided' }}</dd></div>
                        <div><dt>Assigned staff</dt><dd>{{ $blotter->assignee?->name ?? ($blotter->status === \App\Models\Blotter::STATUS_PENDING ? 'Awaiting acceptance' : 'Not recorded') }}</dd></div>
                        @if ($blotter->hearing_at)
                            <div><dt>Mediation schedule</dt><dd><time datetime="{{ $blotter->hearing_at->toIso8601String() }}">{{ $blotter->hearing_at->timezone('Asia/Manila')->format('M j, Y \a\t g:i A') }}</time></dd></div>
                        @endif
                        <div class="staff-request-field-long"><dt>Incident details</dt><dd>{{ $blotter->incident }}</dd></div>
                    </dl>
                </section>

                <section class="staff-request-detail-card incident-action-card" aria-labelledby="blotter-action-title">
                <div class="staff-request-card-heading">
                    <span class="staff-request-card-icon"><i data-lucide="badge-check" aria-hidden="true"></i></span>
                    <div><h2 id="blotter-action-title">Handle this case</h2><p>{{ $blotter->serviceRequest ? 'Progress messages are visible to the resident.' : 'Move the case through the mediation process.' }}</p></div>
                </div>

                @if ($blotter->status === \App\Models\Blotter::STATUS_PENDING)
                    <form method="POST" action="{{ route('blotters.status.update', $blotter) }}" class="incident-action-form">
                        @csrf
                        <input type="hidden" name="action" value="accept">
                        <p class="incident-action-help">Accepting assigns this case to you. You can then set its mediation schedule.</p>
                        <details class="complaint-optional-fields" @if ($errors->has('message')) open @endif>
                            <summary>Add an update message <span>(optional)</span></summary>
                            <div class="complaint-optional-body"><div class="resident-field"><x-form.label for="message">Case update</x-form.label><x-form.textarea name="message" rows="4" maxlength="2000" placeholder="Add instructions or a short update." :aria-invalid="$errors->has('message') ? 'true' : 'false'">{{ old('message') }}</x-form.textarea><x-form.error :message="$errors->first('message')" /></div></div>
                        </details>
                        <button type="submit" class="button button-primary">Accept case <i data-lucide="arrow-right" aria-hidden="true"></i></button>
                    </form>
                @elseif ($blotter->assigned_to === auth()->id() && $blotter->status === \App\Models\Blotter::STATUS_ACCEPTED)
                    <form method="POST" action="{{ route('blotters.status.update', $blotter) }}" class="incident-action-form">
                        @csrf
                        <input type="hidden" name="action" value="schedule">
                        <p class="incident-action-help">Choose when the complainant and respondent should attend barangay mediation.</p>
                        <div class="resident-field">
                            <x-form.label for="hearing_at" required>Mediation schedule</x-form.label>
                            <x-form.input type="datetime-local" name="hearing_at" :value="old('hearing_at')" :min="now('Asia/Manila')->format('Y-m-d\TH:i')" required :aria-invalid="$errors->has('hearing_at') ? 'true' : 'false'" />
                            <x-form.error :message="$errors->first('hearing_at')" />
                        </div>
                        <details class="complaint-optional-fields" @if ($errors->has('message')) open @endif>
                            <summary>Add an update message <span>(optional)</span></summary>
                            <div class="complaint-optional-body"><div class="resident-field"><x-form.label for="message">Case update</x-form.label><x-form.textarea name="message" rows="4" maxlength="2000">{{ old('message') }}</x-form.textarea><x-form.error :message="$errors->first('message')" /></div></div>
                        </details>
                        <button type="submit" class="button button-primary"><i data-lucide="calendar-clock" aria-hidden="true"></i> Save schedule</button>
                    </form>
                @elseif ($blotter->assigned_to === auth()->id() && $blotter->status === \App\Models\Blotter::STATUS_SCHEDULED)
                    <form method="POST" action="{{ route('blotters.status.update', $blotter) }}" class="incident-action-form">
                        @csrf
                        <input type="hidden" name="action" value="start">
                        <p class="incident-action-help">Scheduled for <strong>{{ $blotter->hearing_at?->timezone('Asia/Manila')->format('M j, Y \a\t g:i A') }}</strong>. Start this step when the mediation session begins.</p>
                        <details class="complaint-optional-fields" @if ($errors->has('message')) open @endif>
                            <summary>Add an update message <span>(optional)</span></summary>
                            <div class="complaint-optional-body"><div class="resident-field"><x-form.label for="message">Case update</x-form.label><x-form.textarea name="message" rows="4" maxlength="2000">{{ old('message') }}</x-form.textarea><x-form.error :message="$errors->first('message')" /></div></div>
                        </details>
                        <button type="submit" class="button button-primary">Start mediation <i data-lucide="arrow-right" aria-hidden="true"></i></button>
                    </form>
                @elseif ($blotter->assigned_to === auth()->id() && $blotter->status === \App\Models\Blotter::STATUS_MEDIATION)
                    <form method="POST" action="{{ route('blotters.status.update', $blotter) }}" class="incident-action-form">
                        @csrf
                        <p class="incident-action-help">Record the final outcome after mediation. This closes the case.</p>
                        <div class="resident-field"><x-form.label for="message">Outcome notes</x-form.label><x-form.textarea name="message" rows="4" maxlength="2000" placeholder="Summarize the outcome or next instructions." :aria-invalid="$errors->has('message') ? 'true' : 'false'">{{ old('message') }}</x-form.textarea><x-form.error :message="$errors->first('message')" /></div>
                        <div class="staff-request-review-actions">
                            <button type="submit" name="action" value="settle" class="button button-primary"><i data-lucide="check" aria-hidden="true"></i> Mark settled</button>
                            <button type="submit" name="action" value="dismiss" class="button button-danger-outline">Dismiss case</button>
                        </div>
                    </form>
                @else
                    <p class="incident-action-help">
                        @if (in_array($blotter->status, [\App\Models\Blotter::STATUS_SETTLED, \App\Models\Blotter::STATUS_DISMISSED], true))
                            This case is closed with the outcome <strong>{{ strtolower($blotter->statusLabel()) }}</strong>.
                        @else
                            Assigned to <strong>{{ $blotter->assignee?->name ?? 'another staff member' }}</strong>. Only the assigned staff member can update its progress.
                        @endif
                    </p>
                @endif
                </section>
            </div>

            <aside class="staff-request-detail-card incident-staff-timeline" aria-labelledby="blotter-history-title">
                <div class="staff-request-card-heading">
                    <span class="staff-request-card-icon"><i data-lucide="history" aria-hidden="true"></i></span>
                    <div><h2 id="blotter-history-title">Case history</h2><p>Recorded progress from filing through the final outcome</p></div>
                </div>
                <ol class="incident-timeline">
                    <li>
                        <span class="incident-timeline-dot" aria-hidden="true"></span>
                        <div>
                            <x-blotter-status :status="\App\Models\Blotter::STATUS_PENDING" />
                            <time datetime="{{ $blotter->created_at->toIso8601String() }}">{{ $blotter->created_at->timezone('Asia/Manila')->format('M j, Y \a\t g:i A') }}</time>
                            <p>The blotter case was recorded and is waiting for staff review.</p>
                        </div>
                    </li>
                    @foreach ($blotter->updates as $update)
                        <li>
                            <span class="incident-timeline-dot" aria-hidden="true"></span>
                            <div>
                                <x-blotter-status :status="$update->status" />
                                <time datetime="{{ $update->created_at->toIso8601String() }}">{{ $update->created_at->timezone('Asia/Manila')->format('M j, Y \a\t g:i A') }}</time>
                                <p>{{ $update->message }}</p>
                            </div>
                        </li>
                    @endforeach
                    @if ($blotter->updates->isEmpty() && $blotter->status !== \App\Models\Blotter::STATUS_PENDING)
                        <li>
                            <span class="incident-timeline-dot" aria-hidden="true"></span>
                            <div>
                                <x-blotter-status :status="$blotter->status" />
                                <time datetime="{{ $blotter->updated_at->toIso8601String() }}">{{ $blotter->updated_at->timezone('Asia/Manila')->format('M j, Y \a\t g:i A') }}</time>
                                <p>This status was carried over from the existing blotter record.</p>
                            </div>
                        </li>
                    @endif
                </ol>
            </aside>
        </div>
    </div>
@endsection
