<?php

namespace App\Support;

use App\Models\Certificate;
use App\Models\Payment;
use App\Models\ServiceRequest;

class CertificateIssuance
{
    public function issue(ServiceRequest $serviceRequest, Payment $payment): void
    {
        if ($serviceRequest->certificate_id !== null
            || $serviceRequest->status !== ServiceRequest::STATUS_AWAITING_PAYMENT) {
            return;
        }

        $certificate = Certificate::create([
            'resident_id' => $serviceRequest->resident_id,
            'certificate_type' => $serviceRequest->certificate_type,
            'purpose' => $serviceRequest->purpose,
            'fee' => $payment->amount,
            'date_issued' => today(),
        ]);

        $serviceRequest->update([
            'status' => ServiceRequest::STATUS_COMPLETED,
            'certificate_id' => $certificate->id,
        ]);
    }
}
