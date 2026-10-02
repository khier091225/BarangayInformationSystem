<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Blotter extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'Pending';

    public const STATUS_ACCEPTED = 'Accepted';

    public const STATUS_SCHEDULED = 'Scheduled';

    public const STATUS_MEDIATION = 'Mediation';

    public const STATUS_SETTLED = 'Settled';

    public const STATUS_DISMISSED = 'Dismissed';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_ACCEPTED,
        self::STATUS_SCHEDULED,
        self::STATUS_MEDIATION,
        self::STATUS_SETTLED,
        self::STATUS_DISMISSED,
    ];

    public const ACTIVE_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_ACCEPTED,
        self::STATUS_SCHEDULED,
        self::STATUS_MEDIATION,
    ];

    protected $fillable = [
        'complainant',
        'respondent',
        'incident',
        'incident_date',
        'status',
        'assigned_to',
        'accepted_at',
        'hearing_at',
        'mediation_started_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'incident_date' => 'date',
            'accepted_at' => 'datetime',
            'hearing_at' => 'datetime',
            'mediation_started_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(BlotterUpdate::class)->oldest('id');
    }

    public function serviceRequest(): HasOne
    {
        return $this->hasOne(ServiceRequest::class);
    }

    public function statusLabel(): string
    {
        return $this->status === self::STATUS_MEDIATION ? 'Under Mediation' : $this->status;
    }
}
