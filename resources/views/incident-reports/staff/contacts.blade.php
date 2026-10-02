@extends('layouts.app')

@section('title', 'Incident Alert Contacts | Barangay Information System')

@section('breadcrumb')
    <a href="{{ route('incident-reports.index') }}">Incident reports</a><i data-lucide="chevron-right" aria-hidden="true"></i><strong>Alert contacts</strong>
@endsection

@section('content')
    <div class="incident-contacts-page">
        <x-workspace.page-header title="Incident alert contacts" description="Manage the duty phone and email used when a resident submits a new incident report." icon="bell">
            <x-slot:actions>
                <a href="{{ route('incident-reports.index') }}" class="button button-primary"><i data-lucide="clipboard-list" aria-hidden="true"></i> Incident reports</a>
            </x-slot:actions>
        </x-workspace.page-header>

        <div class="incident-contact-guidance" role="note">
            <i data-lucide="info" aria-hidden="true"></i>
            <div><strong>Changes apply to future alerts.</strong><span>Account email changes do not update these duty contacts. Turn off a channel or deactivate a contact when it should stop receiving new alerts.</span></div>
        </div>

        <div class="incident-contact-grid">
            @foreach ($contactCards as $card)
                @php
                    $team = $card['team'];
                    $hasErrors = old('team_key') === $team;
                    $values = $card['values'];
                @endphp
                <section class="incident-contact-card" id="contact-{{ $team }}" aria-labelledby="contact-{{ $team }}-title">
                    <div class="incident-contact-card-heading">
                        <div><span class="incident-contact-team-icon"><i data-lucide="{{ $team === 'tanod' ? 'shield-check' : ($team === 'maintenance' ? 'building-2' : ($team === 'leadership' ? 'landmark' : 'files')) }}" aria-hidden="true"></i></span><div><h2 id="contact-{{ $team }}-title">{{ $card['label'] }}</h2><span class="incident-contact-source incident-contact-source-{{ $card['source'] }}">{{ $card['source'] === 'database' ? 'Database managed' : 'Environment fallback' }}</span></div></div>
                        @if ($card['contact'])
                            <span class="incident-contact-state {{ $card['contact']->is_active ? 'is-active' : 'is-inactive' }}">{{ $card['contact']->is_active ? 'Active' : 'Inactive' }}</span>
                        @endif
                    </div>

                    @if ($card['source'] === 'environment')
                        <p class="incident-contact-fallback">Save this team to move its alert contact from the server environment into the database.</p>
                    @endif

                    <form method="POST" action="{{ route('incident-report-contacts.update', $team) }}" class="incident-contact-form">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="team_key" value="{{ $team }}">

                        <div class="incident-contact-fields">
                            <div class="incident-contact-field incident-contact-field-wide">
                                <x-form.label for="{{ $team }}_contact_name">Contact person or desk</x-form.label>
                                <x-form.input name="contact_name" id="{{ $team }}_contact_name" :value="$hasErrors ? old('contact_name') : $values['contact_name']" maxlength="100" placeholder="e.g. Duty Officer" autocomplete="off" :aria-invalid="$hasErrors && $errors->has('contact_name') ? 'true' : 'false'" />
                                @if ($hasErrors)<x-form.error :message="$errors->first('contact_name')" />@endif
                            </div>
                            <div class="incident-contact-field">
                                <x-form.label for="{{ $team }}_phone">Mobile number</x-form.label>
                                <x-form.input name="phone" id="{{ $team }}_phone" type="tel" :value="$hasErrors ? old('phone') : $values['phone']" maxlength="32" inputmode="tel" placeholder="09XXXXXXXXX" autocomplete="off" :aria-invalid="$hasErrors && $errors->has('phone') ? 'true' : 'false'" />
                                @if ($hasErrors)<x-form.error :message="$errors->first('phone')" />@endif
                            </div>
                            <div class="incident-contact-field">
                                <x-form.label for="{{ $team }}_email">Email address</x-form.label>
                                <x-form.input name="email" id="{{ $team }}_email" type="email" :value="$hasErrors ? old('email') : $values['email']" maxlength="255" placeholder="e.g. duty@example.com" autocomplete="off" :aria-invalid="$hasErrors && $errors->has('email') ? 'true' : 'false'" />
                                @if ($hasErrors)<x-form.error :message="$errors->first('email')" />@endif
                            </div>
                        </div>

                        <div class="incident-contact-toggles">
                            <input type="hidden" name="sms_enabled" value="0">
                            <label><input type="checkbox" name="sms_enabled" value="1" @checked($hasErrors ? old('sms_enabled') : $values['sms_enabled'])><span><strong>SMS alerts</strong><small>Send new reports to the mobile number.</small></span></label>
                            <input type="hidden" name="email_enabled" value="0">
                            <label><input type="checkbox" name="email_enabled" value="1" @checked($hasErrors ? old('email_enabled') : $values['email_enabled'])><span><strong>Email alerts</strong><small>Send new reports to the email address.</small></span></label>
                            <input type="hidden" name="is_active" value="0">
                            <label><input type="checkbox" name="is_active" value="1" @checked($hasErrors ? old('is_active') : $values['is_active'])><span><strong>Active duty contact</strong><small>Allow enabled channels to receive future alerts.</small></span></label>
                        </div>
                        @if ($hasErrors)<x-form.error :message="$errors->first('is_active')" />@endif

                        <div class="incident-contact-actions">
                            <button type="submit" class="button button-primary"><i data-lucide="save" aria-hidden="true"></i> Save contact</button>
                            @if ($card['contact'])
                                <span>Last updated {{ $card['contact']->updated_at->timezone('Asia/Manila')->format('M j, Y \a\t g:i A') }}{{ $card['contact']->updater ? ' by '.$card['contact']->updater->name : '' }}</span>
                            @endif
                        </div>
                    </form>
                </section>
            @endforeach
        </div>

        <section class="incident-contact-history" aria-labelledby="incident-contact-history-title">
            <div class="incident-contact-history-heading"><div><h2 id="incident-contact-history-title">Recent contact changes</h2><p>The system records who updated duty contact settings.</p></div><span class="incident-contact-team-icon"><i data-lucide="history" aria-hidden="true"></i></span></div>
            @if ($recentChanges->isEmpty())
                <p class="incident-contact-history-empty">No database-managed contact changes yet.</p>
            @else
                <ol>
                    @foreach ($recentChanges as $change)
                        <li><div><strong>{{ $change->team_label }}</strong><span>{{ collect($change->changed_fields)->map(fn ($field) => str($field)->replace('_', ' ')->headline())->join(', ') }}</span></div><div><span>{{ $change->changed_by_name ?? 'System' }}</span><time datetime="{{ $change->changed_at->toIso8601String() }}">{{ $change->changed_at->timezone('Asia/Manila')->format('M j, Y \a\t g:i A') }}</time></div></li>
                    @endforeach
                </ol>
            @endif
        </section>
    </div>
@endsection
