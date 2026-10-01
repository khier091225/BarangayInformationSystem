<?php

namespace Tests\Feature;

use App\Mail\IncidentReportAlert;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailTemplateTest extends TestCase
{
    public function test_password_reset_email_has_a_working_link_expiry_and_embedded_branding_in_both_formats(): void
    {
        config()->set('mail.default', 'array');
        config()->set('auth.passwords.users.expire', 45);
        $user = User::factory()->make(['name' => 'Ana <strong>Santos</strong>']);
        $token = 'sample-reset-token';

        $user->sendPasswordResetNotification($token);

        $email = Mail::mailer('array')->getSymfonyTransport()->messages()->sole()->getOriginalMessage();
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);
        $this->assertSame('Reset your password | Barangay Kay-Anlog', $email->getSubject());
        $this->assertSame($user->email, $email->getTo()[0]->getAddress());
        $this->assertStringContainsString('Hello '.e($user->name), $email->getHtmlBody());
        $this->assertStringNotContainsString('<strong>Santos</strong>', $email->getHtmlBody());
        $this->assertStringContainsString('href="'.e($resetUrl).'"', $email->getHtmlBody());
        $this->assertStringContainsString('This link expires in 45 minutes.', $email->getHtmlBody());
        $this->assertStringContainsString('#276747', $email->getHtmlBody());
        $this->assertStringContainsString('src="cid:', $email->getHtmlBody());
        $this->assertCount(1, $email->getAttachments());
        $this->assertSame('inline', $email->getAttachments()[0]->getDisposition());
        $this->assertStringContainsString('Hello '.$user->name, $email->getTextBody());
        $this->assertStringContainsString($resetUrl, $email->getTextBody());
        $this->assertStringContainsString('This link expires in 45 minutes.', $email->getTextBody());
        $this->assertStringNotContainsString('<table', $email->getTextBody());
    }

    public function test_incident_alert_includes_details_privacy_notice_and_embedded_branding_in_both_formats(): void
    {
        config()->set('mail.default', 'array');
        $mail = new IncidentReportAlert(
            'INC-2026-000123',
            'Noise Complaint',
            'Purok 3 & covered court',
            'Oct 1, 2026 9:30 PM',
            "Loud music near the court.\nPlease send the duty team.",
            true,
        );

        Mail::to('duty@example.test')->send($mail);

        $email = Mail::mailer('array')->getSymfonyTransport()->messages()->sole()->getOriginalMessage();
        $this->assertSame('Incident report INC-2026-000123 | Barangay Kay-Anlog', $email->getSubject());
        $this->assertStringContainsString('#123c35', $email->getHtmlBody());
        $this->assertStringContainsString('src="cid:', $email->getHtmlBody());
        $this->assertCount(1, $email->getAttachments());
        $this->assertSame('inline', $email->getAttachments()[0]->getDisposition());
        $this->assertStringContainsString('Confidential report', $email->getHtmlBody());
        $this->assertStringContainsString('CONFIDENTIAL REPORT', $email->getTextBody());
        $this->assertStringContainsString('Loud music near the court.<br', $email->getHtmlBody());

        foreach ([$mail->referenceNumber, $mail->categoryLabel, $mail->location, $mail->occurredAt, route('incident-reports.index')] as $detail) {
            $this->assertStringContainsString(e($detail), $email->getHtmlBody());
            $this->assertStringContainsString($detail, $email->getTextBody());
        }

        $this->assertStringContainsString($mail->description, $email->getTextBody());
        $this->assertStringNotContainsString('<table', $email->getTextBody());
    }

    public function test_incident_alert_omits_the_confidential_notice_when_it_was_not_requested(): void
    {
        $mail = new IncidentReportAlert(
            'INC-2026-000124',
            'Noise Complaint',
            'Purok 3',
            'Oct 1, 2026 9:30 PM',
            'Loud music near the court.',
            false,
        );

        $mail->assertDontSeeInHtml('Confidential report');
        $mail->assertDontSeeInText('CONFIDENTIAL REPORT');
        $mail->assertSeeInHtml('Resident identity and attachments are not included in this email.');
    }

    public function test_incident_alert_escapes_resident_content_in_html_and_preserves_it_in_plain_text(): void
    {
        $location = '<img src=x onerror=alert(1)> & Purok 3';
        $description = '<script>alert("incident")</script>';
        $mail = new IncidentReportAlert(
            'INC-2026-000125',
            'Noise Complaint',
            $location,
            'Oct 1, 2026 9:30 PM',
            $description,
            false,
        );

        $mail->assertSeeInHtml($location);
        $mail->assertDontSeeInHtml($location, false);
        $mail->assertSeeInHtml($description);
        $mail->assertDontSeeInHtml($description, false);
        $mail->assertSeeInText($location);
        $mail->assertSeeInText($description);
    }
}
