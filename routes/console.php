<?php

use App\Http\PayMongo;
use App\Http\PayMongoPayments;
use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('payments:sync {--limit=20 : Maximum pending payments to check}', function (PayMongoPayments $payments): int {
    if (! $payments->gateway->isConfigured()) {
        $this->error('Online payment is not configured yet. Set the PayMongo keys and public HTTPS URL first.');

        return Command::FAILURE;
    }

    $limit = filter_var($this->option('limit'), FILTER_VALIDATE_INT);
    if ($limit === false || $limit < 1 || $limit > 100) {
        $this->error('The limit must be between 1 and 100.');

        return Command::FAILURE;
    }

    $pending = Payment::query()->where('provider', Payment::PROVIDER_PAYMONGO_QRPH)
        ->where('status', Payment::STATUS_PENDING)->whereNotNull('provider_reference')
        ->where('provider_livemode', $payments->gateway->isLiveMode())
        ->where(function (Builder $query): void {
            $query->whereNull('provider_checked_at')->orWhere('provider_checked_at', '<=', now()->subSeconds(30));
        })->orderBy('provider_checked_at')->orderBy('id')->limit($limit)->get();
    $failures = 0;
    foreach ($pending as $payment) {
        try {
            $payments->synchronize($payment);
        } catch (ValidationException|HttpExceptionInterface) {
            $failures++;
            Log::warning('A pending payment could not be synchronized.', ['payment_id' => $payment->public_id]);
        }
    }

    $this->info("{$pending->count()} payment record(s) checked; {$failures} could not be confirmed.");

    return $failures === 0 ? Command::SUCCESS : Command::FAILURE;
})->purpose('Confirm pending QR Ph payments and close expired checkouts');

Schedule::command('payments:sync')->everyMinute()->withoutOverlapping(30)
    ->when(fn (): bool => app(PayMongo::class)->isConfigured());
