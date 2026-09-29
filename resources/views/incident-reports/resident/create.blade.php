@extends('layouts.resident')

@section('title', 'Report an Incident | Barangay Information System')
@section('main-class', 'resident-main-form')

@section('content')
    <div class="resident-page-heading">
        <div>
            <span class="resident-kicker resident-kicker-dark">ONLINE SUMBONG</span>
            <h1>Report an incident</h1>
            <p>Tell barangay staff what happened. You can follow their response using your report reference.</p>
        </div>
        <a href="{{ route('account.incidents.index') }}" class="resident-inline-link">My reports <i data-lucide="arrow-right" aria-hidden="true"></i></a>
    </div>

    <section class="resident-card resident-form-card resident-service-form" aria-label="Incident report form">
        <div class="resident-form-intro">
            <span class="resident-section-icon"><i data-lucide="message-square-warning" aria-hidden="true"></i></span>
            <div><span>Submitting as</span><strong>{{ auth()->user()->name }}</strong></div>
            <p>Fields marked * are required.</p>
        </div>
        <form method="POST" action="{{ route('account.incidents.store') }}" enctype="multipart/form-data" class="resident-form">
            @csrf
            <div class="resident-form-fields">
                <div class="resident-field">
                    <x-form.label for="category" required>Category</x-form.label>
                    <x-form.select name="category" required :aria-invalid="$errors->has('category') ? 'true' : 'false'" :aria-describedby="$errors->has('category') ? 'category-error' : null">
                        <option value="">Choose the closest category</option>
                        @foreach ($categories as $value => [$label, $team])
                            <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
                        @endforeach
                    </x-form.select>
                    <x-form.error id="category-error" :message="$errors->first('category')" />
                </div>
                <div class="resident-field resident-field-wide">
                    <x-form.label for="location" required>Location / purok / street</x-form.label>
                    <x-form.input name="location" :value="old('location')" required maxlength="255" placeholder="e.g. Purok 3, near the basketball court" :aria-invalid="$errors->has('location') ? 'true' : 'false'" :aria-describedby="$errors->has('location') ? 'location-error' : null" />
                    <x-form.error id="location-error" :message="$errors->first('location')" />
                </div>
                <div class="resident-field resident-field-wide">
                    <x-form.label for="description" required>What happened?</x-form.label>
                    <x-form.textarea name="description" rows="6" required minlength="10" maxlength="5000" placeholder="Describe what you observed and any details that may help staff respond." :aria-invalid="$errors->has('description') ? 'true' : 'false'" :aria-describedby="$errors->has('description') ? 'description-error incident-description-help' : 'incident-description-help'">{{ old('description') }}</x-form.textarea>
                    <p id="incident-description-help" class="incident-field-help">The duty team may receive this location and description by SMS or email. Avoid adding your name or contact number here.</p>
                    <x-form.error id="description-error" :message="$errors->first('description')" />
                </div>
            </div>
            <details class="complaint-optional-fields" @if ($errors->has('occurred_at') || $errors->has('evidence') || $errors->has('keep_identity_confidential')) open @endif>
                <summary>Add date, photo, or privacy preference <span>(optional)</span></summary>
                <div class="complaint-optional-body">
                    <div class="resident-field">
                        <x-form.label for="occurred_at">When did it happen?</x-form.label>
                        <x-form.input name="occurred_at" type="datetime-local" :value="old('occurred_at')" max="{{ now('Asia/Manila')->format('Y-m-d\TH:i') }}" :aria-invalid="$errors->has('occurred_at') ? 'true' : 'false'" :aria-describedby="$errors->has('occurred_at') ? 'incident-date-help occurred-at-error' : 'incident-date-help'" />
                        <p id="incident-date-help" class="incident-field-help">Leave blank to use the time you submit this report.</p>
                        <x-form.error id="occurred-at-error" :message="$errors->first('occurred_at')" />
                    </div>
                    <div class="resident-field">
                        <x-form.label for="evidence">Photo or video</x-form.label>
                        <x-form.input name="evidence" type="file" accept="image/jpeg,image/png,image/webp,video/mp4,video/quicktime" :aria-invalid="$errors->has('evidence') ? 'true' : 'false'" :aria-describedby="$errors->has('evidence') ? 'evidence-error evidence-help' : 'evidence-help'" />
                        <p id="evidence-help" class="incident-field-help">JPG, PNG, WebP, MP4, or MOV. Maximum 20 MB.</p>
                        <x-form.error id="evidence-error" :message="$errors->first('evidence')" />
                    </div>
                    <label class="incident-confidential-choice">
                        <input type="checkbox" name="keep_identity_confidential" value="1" @checked(old('keep_identity_confidential'))>
                        <span><strong>Keep my identity confidential</strong><small>Your name is hidden from staff who have not accepted this report. The staff member handling the report can see your identity. This is not anonymous.</small></span>
                    </label>
                </div>
            </details>
            <aside class="resident-form-guidance" aria-labelledby="incident-help-title">
                <i data-lucide="info" aria-hidden="true"></i>
                <div><h2 id="incident-help-title">For immediate danger</h2><p>Contact emergency services or the barangay office directly. Online reports may not be reviewed immediately. For a formal blotter record, you can <a href="{{ route('account.requests.blotter.create') }}">file a blotter report</a> separately.</p></div>
            </aside>
            <div class="resident-form-actions"><button type="submit" class="resident-button resident-button-primary">Submit report <i data-lucide="arrow-right" aria-hidden="true"></i></button><a href="{{ route('account') }}" class="resident-button resident-button-outline">Cancel</a></div>
        </form>
    </section>
@endsection
