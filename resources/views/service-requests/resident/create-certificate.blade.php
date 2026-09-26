<div>
    <!-- The whole future lies in uncertainty: live immediately. - Seneca -->
</div>
@extends('layouts.resident')

@section('title', 'Request a Document | Barangay Information System')

@section('content')
    <a href="{{ route('account') }}" class="resident-muted">← Back to dashboard</a>
    <div style="margin: 18px 0 24px;">
        <div class="eyebrow">DOCUMENT REQUEST</div>
        <h1 class="resident-heading">Request a barangay document</h1>
        <p class="resident-muted">Staff will review your request before issuing an official document. You can track its status from your dashboard.</p>
    </div>

    <div class="resident-card" style="max-width: 640px;">
        <p class="resident-muted" style="margin-top: 0;">Requesting for <strong>{{ auth()->user()->name }}</strong></p>
        <form method="POST" action="{{ route('account.requests.certificate.store') }}">
            @csrf
            <div style="margin-bottom: 20px;">
                <x-form.label for="certificate_type" required>Document type</x-form.label>
                <x-form.select name="certificate_type" required>
                    <option value="">Select a document</option>
                    @foreach (['Barangay Clearance', 'Certificate of Residency', 'Certificate of Indigency', 'Business Clearance'] as $type)
                        <option value="{{ $type }}" @selected(old('certificate_type') === $type)>{{ $type }}</option>
                    @endforeach
                </x-form.select>
                <x-form.error :message="$errors->first('certificate_type')" />
            </div>
            <div style="margin-bottom: 24px;">
                <x-form.label for="purpose" required>Purpose</x-form.label>
                <x-form.input name="purpose" :value="old('purpose')" required maxlength="255" placeholder="e.g. Employment, scholarship, or business requirement" />
                <x-form.error :message="$errors->first('purpose')" />
            </div>
            <button type="submit" class="button button-primary">Submit document request</button>
        </form>
    </div>
@endsection
