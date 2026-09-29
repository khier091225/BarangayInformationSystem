@extends('layouts.resident')

@section('title', 'Request a Document | Barangay Information System')
@section('main-class', 'resident-main-form')

@section('content')
    <div class="resident-page-heading">
        <div>
            <h1>Request a barangay document</h1>
            <p>Choose the document you need and enter its purpose. Barangay staff will review your request.</p>
        </div>
        <a href="{{ route('account.requests.index') }}" class="resident-inline-link">My requests <i data-lucide="arrow-right" aria-hidden="true"></i></a>
    </div>

    <section class="resident-card resident-form-card resident-service-form" aria-label="Document request form">
        <div class="resident-form-intro">
            <span class="resident-section-icon"><i data-lucide="user-round" aria-hidden="true"></i></span>
            <div><span>Requesting for</span><strong>{{ auth()->user()->name }}</strong></div>
            <p>Fields marked * are required.</p>
        </div>
        <form method="POST" action="{{ route('account.requests.certificate.store') }}" class="resident-form">
            @csrf
            <div class="resident-form-fields">
                <div class="resident-field">
                    <x-form.label for="certificate_type" required>Document type</x-form.label>
                    <x-form.select name="certificate_type" required :aria-invalid="$errors->has('certificate_type') ? 'true' : 'false'" :aria-describedby="$errors->has('certificate_type') ? 'certificate-type-error' : null">
                        <option value="">Select a document</option>
                        @foreach (['Barangay Clearance', 'Certificate of Residency', 'Certificate of Indigency', 'Business Clearance'] as $type)
                            <option value="{{ $type }}" @selected(old('certificate_type') === $type)>{{ $type }}</option>
                        @endforeach
                    </x-form.select>
                    <x-form.error id="certificate-type-error" :message="$errors->first('certificate_type')" />
                </div>
                <div class="resident-field">
                    <x-form.label for="purpose" required>Purpose</x-form.label>
                    <x-form.input name="purpose" :value="old('purpose')" required maxlength="255" placeholder="e.g. Employment or scholarship" :aria-invalid="$errors->has('purpose') ? 'true' : 'false'" :aria-describedby="$errors->has('purpose') ? 'purpose-error' : null" />
                    <x-form.error id="purpose-error" :message="$errors->first('purpose')" />
                </div>
            </div>
            <aside class="resident-form-guidance" aria-labelledby="request-help-title">
                <i data-lucide="info" aria-hidden="true"></i>
                <div><h2 id="request-help-title">What happens next?</h2><p>Staff will review your request. Follow its status in <a href="{{ route('account.requests.index') }}">My requests</a>. Once issued, contact the barangay office about collection.</p></div>
            </aside>
            <div class="resident-form-actions"><button type="submit" class="resident-button resident-button-primary">Submit document request <i data-lucide="arrow-right" aria-hidden="true"></i></button><a href="{{ route('account') }}" class="resident-button resident-button-outline">Cancel</a></div>
        </form>
    </section>
@endsection
