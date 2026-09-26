@extends('layouts.resident')

@section('title', 'Request a Document | Barangay Information System')

@section('content')
    <a href="{{ route('account') }}" class="resident-page-back"><i data-lucide="arrow-left" aria-hidden="true"></i> Back to dashboard</a>
    <div class="resident-page-heading">
        <div>
            <span class="resident-kicker resident-kicker-dark">DOCUMENT REQUEST</span>
            <h1>Request a barangay document</h1>
            <p>Tell us which document you need and why. Barangay staff will review your request before issuing it.</p>
        </div>
    </div>

    <div class="resident-form-layout">
        <section class="resident-card resident-form-card" aria-label="Document request form">
            <div class="resident-form-intro"><span class="resident-section-icon"><i data-lucide="user-round" aria-hidden="true"></i></span><span>Requesting for <strong>{{ auth()->user()->name }}</strong></span></div>
            <form method="POST" action="{{ route('account.requests.certificate.store') }}" class="resident-form">
                @csrf
                <div class="resident-field">
                    <x-form.label for="certificate_type" required>Document type</x-form.label>
                    <x-form.select name="certificate_type" required>
                        <option value="">Select a document</option>
                        @foreach (['Barangay Clearance', 'Certificate of Residency', 'Certificate of Indigency', 'Business Clearance'] as $type)
                            <option value="{{ $type }}" @selected(old('certificate_type') === $type)>{{ $type }}</option>
                        @endforeach
                    </x-form.select>
                    <x-form.error :message="$errors->first('certificate_type')" />
                </div>
                <div class="resident-field">
                    <x-form.label for="purpose" required>Purpose</x-form.label>
                    <x-form.input name="purpose" :value="old('purpose')" required maxlength="255" placeholder="e.g. Employment or scholarship" />
                    <x-form.error :message="$errors->first('purpose')" />
                </div>
                <button type="submit" class="resident-button resident-button-primary">Submit document request <i data-lucide="arrow-right" aria-hidden="true"></i></button>
            </form>
        </section>
        <aside class="resident-card resident-form-help" aria-labelledby="request-help-title">
            <span class="resident-section-icon"><i data-lucide="info" aria-hidden="true"></i></span>
            <h2 id="request-help-title">What happens next?</h2>
            <p>After you submit, your request will appear in My requests with a Pending status.</p>
            <ul>
                <li>Barangay staff will check the request details.</li>
                <li>You can check its status from your dashboard.</li>
                <li>Staff will provide collection instructions when the document is issued.</li>
            </ul>
        </aside>
    </div>
@endsection
