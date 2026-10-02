@extends('layouts.resident')

@section('title', 'Request a Document | Barangay Information System')
@section('main-class', 'resident-main-form')

@section('content')
    <x-workspace.page-header title="Request a barangay document" description="Choose the document you need, check its fixed fee, and enter its purpose." icon="files">
        <x-slot:actions>
            <a href="{{ route('account.requests.index', ['type' => 'certificate']) }}" class="resident-button resident-button-primary"><i data-lucide="inbox" aria-hidden="true"></i> My document requests</a>
        </x-slot:actions>
    </x-workspace.page-header>

    <section class="resident-card resident-form-card resident-service-form" aria-label="Document request form">
        <div class="resident-form-intro">
            <span class="resident-section-icon"><i data-lucide="user-round" aria-hidden="true"></i></span>
            <div><span>Requesting for</span><strong>{{ auth()->user()->name }}</strong></div>
            <p>Fields marked * are required.</p>
        </div>
        <form method="POST" action="{{ route('account.requests.certificate.store') }}" class="resident-form resident-service-grid">
            @csrf
            <div class="resident-form-fields resident-form-fields-stacked">
                <div class="resident-field">
                    <x-form.label for="certificate_type" required>Document type</x-form.label>
                    <x-form.select name="certificate_type" required :aria-invalid="$errors->has('certificate_type') ? 'true' : 'false'" :aria-describedby="$errors->has('certificate_type') ? 'certificate-type-error' : null">
                        <option value="">Select a document</option>
                        @foreach ($certificateFees as $type => $fee)
                            <option value="{{ $type }}" @selected(old('certificate_type') === $type)>{{ $type }} — {{ (float) $fee === 0.0 ? 'Free' : '₱'.number_format((float) $fee, 2) }}</option>
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
                <div><h2 id="request-help-title">What happens next?</h2><p>The displayed fee is fixed for the selected document. Staff will verify your request before payment. Follow its status in <a href="{{ route('account.requests.index', ['type' => 'certificate']) }}">My document requests</a>.</p></div>
            </aside>
            <div class="resident-form-actions"><button type="submit" class="resident-button resident-button-primary">Submit document request <i data-lucide="arrow-right" aria-hidden="true"></i></button><a href="{{ route('account') }}" class="resident-button resident-button-outline">Cancel</a></div>
        </form>
    </section>
@endsection
