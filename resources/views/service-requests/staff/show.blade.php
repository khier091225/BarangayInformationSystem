<div>
    <!-- I have not failed. I've just found 10,000 ways that won't work. - Thomas Edison -->
</div>
@extends('layouts.app')

@section('title', 'Review Resident Request | Barangay Information System')

@section('breadcrumb')
    <a href="{{ route('service-requests.index') }}">Resident requests</a><i data-lucide="chevron-right"></i><strong>Request #{{ $serviceRequest->id }}</strong>
@endsection

@section('content')
    <a href="{{ route('service-requests.index') }}" style="color: #42634e; font-size: 13px;">← Back to requests</a>
    <div style="margin: 18px 0 24px;">
        <div class="eyebrow">REQUEST #{{ str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT) }}</div>
        <h1 style="font-size: 26px; color: #1e3a29; margin: 4px 0 8px;">{{ $serviceRequest->type === 'certificate' ? $serviceRequest->certificate_type : 'Blotter report' }}</h1>
        <p style="color: #69786b; font-size: 13px;">Submitted {{ $serviceRequest->created_at->format('M d, Y h:i A') }} · Status: <strong>{{ $serviceRequest->status }}</strong></p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; align-items: start;">
        <div style="background: white; border: 1px solid #e1e7de; border-radius: 8px; padding: 24px;">
            <h2 style="font-size: 18px; margin-top: 0;">Request details</h2>
            <p><strong>Resident:</strong> <a href="{{ route('residents.show', $serviceRequest->resident) }}">{{ $serviceRequest->resident->full_name }}</a></p>
            <p><strong>Address:</strong> {{ $serviceRequest->resident->address }}</p>
            @if ($serviceRequest->type === 'certificate')
                <p><strong>Document:</strong> {{ $serviceRequest->certificate_type }}</p>
                <p><strong>Purpose:</strong> {{ $serviceRequest->purpose }}</p>
            @else
                <p><strong>Complainant:</strong> {{ $serviceRequest->resident->full_name }}</p>
                <p><strong>Respondent:</strong> {{ $serviceRequest->respondent }}</p>
                <p><strong>Incident date:</strong> {{ $serviceRequest->incident_date->format('M d, Y') }}</p>
                <p style="white-space: pre-wrap;"><strong>Incident:</strong> {{ $serviceRequest->incident }}</p>
            @endif
        </div>

        <div style="background: white; border: 1px solid #e1e7de; border-radius: 8px; padding: 24px;">
            @if ($serviceRequest->status === 'Pending')
                <h2 style="font-size: 18px; margin-top: 0;">Review request</h2>
                <p style="font-size: 13px; color: #69786b;">Completing this request creates an official {{ $serviceRequest->type === 'certificate' ? 'certificate' : 'blotter' }} record. A response is required when declining.</p>
                <form method="POST" action="{{ route('service-requests.review', $serviceRequest) }}">
                    @csrf
                    <div style="margin: 18px 0;">
                        <x-form.label for="response_note">Message to resident</x-form.label>
                        <x-form.textarea name="response_note" rows="4" maxlength="1000" placeholder="Collection instructions or reason for declining">{{ old('response_note') }}</x-form.textarea>
                        <x-form.error :message="$errors->first('response_note')" />
                    </div>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <button type="submit" name="decision" value="complete" class="button button-primary">Complete request</button>
                        <button type="submit" name="decision" value="decline" class="button button-outline">Decline request</button>
                    </div>
                    <x-form.error :message="$errors->first('decision')" />
                </form>
            @else
                <h2 style="font-size: 18px; margin-top: 0;">Review outcome</h2>
                <p><strong>Status:</strong> {{ $serviceRequest->status }}</p>
                <p><strong>Reviewed by:</strong> {{ $serviceRequest->reviewer?->name ?? 'Staff account unavailable' }}</p>
                <p style="white-space: pre-wrap;"><strong>Message to resident:</strong> {{ $serviceRequest->response_note ?: 'No message provided.' }}</p>
                @if ($serviceRequest->certificate)
                    <a href="{{ route('certificates.show', $serviceRequest->certificate) }}" class="button button-primary" style="display: inline-block; text-decoration: none;">View issued certificate</a>
                @elseif ($serviceRequest->blotter)
                    <a href="{{ route('blotters.show', $serviceRequest->blotter) }}" class="button button-primary" style="display: inline-block; text-decoration: none;">View blotter record</a>
                @endif
            @endif
        </div>
    </div>
@endsection
