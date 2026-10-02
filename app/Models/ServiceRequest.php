<?php

namespace App\Models;

use Database\Factories\ServiceRequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServiceRequest extends Model
{
    /** @use HasFactory<ServiceRequestFactory> */
    use HasFactory;

    public const TYPE_CERTIFICATE = 'certificate';

    public const TYPE_BLOTTER = 'blotter';

    public const STATUS_PENDING = 'Pending';

    public const STATUS_AWAITING_PAYMENT = 'Awaiting Payment';

    public const STATUS_COMPLETED = 'Completed';

    public const STATUS_DECLINED = 'Declined';

    protected $fillable = [
        'resident_id', 'type', 'certificate_type', 'purpose', 'fee_amount', 'respondent', 'incident',
        'incident_date', 'status', 'response_note', 'reviewed_by', 'reviewed_at',
        'certificate_id', 'blotter_id',
    ];

    protected function casts(): array
    {
        return [
            'incident_date' => 'date',
            'reviewed_at' => 'datetime',
            'fee_amount' => 'decimal:2',
        ];
    }

    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function certificate(): BelongsTo
    {
        return $this->belongsTo(Certificate::class);
    }

    public function blotter(): BelongsTo
    {
        return $this->belongsTo(Blotter::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }
}
