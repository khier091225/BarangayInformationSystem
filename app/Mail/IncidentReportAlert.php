<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IncidentReportAlert extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $referenceNumber,
        public string $categoryLabel,
        public string $location,
        public string $occurredAt,
        public string $description,
        public bool $identityConfidential,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Incident report {$this->referenceNumber} | Barangay Kay-Anlog",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.incident-report-alert',
            text: 'emails.incident-report-alert-text',
        );
    }
}
