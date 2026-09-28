<?php

namespace App\Models;

use Database\Factories\IncidentReportFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IncidentReport extends Model
{
    /** @use HasFactory<IncidentReportFactory> */
    use HasFactory;

    public const STATUS_SUBMITTED = 'Submitted';

    public const STATUS_ASSIGNED = 'Assigned';

    public const STATUS_RESPONDING = 'Responding';

    public const STATUS_RESOLVED = 'Resolved';

    public const STATUS_CLOSED = 'Closed';

    public const CATEGORY_TEAMS = [
        'noise_complaint' => ['Noise Complaint', 'tanod'],
        'domestic_disturbance' => ['Domestic Disturbance', 'leadership'],
        'public_disturbance' => ['Public Disturbance', 'tanod'],
        'illegal_parking' => ['Illegal Parking / Road Obstruction', 'tanod'],
        'garbage_sanitation' => ['Garbage / Sanitation', 'maintenance'],
        'infrastructure' => ['Streetlight / Infrastructure', 'maintenance'],
        'suspicious_activity' => ['Suspicious Activity', 'leadership'],
        'other' => ['Other', 'admin'],
    ];

    public const TEAM_LABELS = [
        'tanod' => 'Barangay Tanod / Duty Officer',
        'maintenance' => 'Barangay Maintenance',
        'admin' => 'Barangay Secretary / Admin',
        'leadership' => 'Barangay Captain / Authorized Staff',
    ];

    protected $fillable = [
        'resident_id', 'category', 'description', 'location', 'occurred_at',
        'keep_identity_confidential', 'evidence_path', 'evidence_original_name',
        'evidence_mime', 'status', 'suggested_team', 'assigned_team', 'assigned_to',
        'assigned_at', 'responding_at', 'resolved_at', 'closed_at',
        'alert_sms_status', 'alert_email_status',
    ];

    protected static function booted(): void
    {
        static::created(function (self $report): void {
            $report->forceFill([
                'reference_number' => 'INC-'.$report->created_at->timezone('Asia/Manila')->format('Y').'-'.str_pad((string) $report->id, 6, '0', STR_PAD_LEFT),
            ])->saveQuietly();
        });
    }

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'keep_identity_confidential' => 'boolean',
            'assigned_at' => 'datetime',
            'responding_at' => 'datetime',
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(IncidentReportUpdate::class)->oldest('id');
    }

    public function categoryLabel(): string
    {
        return self::CATEGORY_TEAMS[$this->category][0] ?? 'Other';
    }

    public function suggestedTeamLabel(): string
    {
        return self::TEAM_LABELS[$this->suggested_team] ?? self::TEAM_LABELS['admin'];
    }

    public function assignedTeamLabel(): ?string
    {
        return $this->assigned_team === null ? null : (self::TEAM_LABELS[$this->assigned_team] ?? null);
    }

    public function nextStatus(): ?string
    {
        return match ($this->status) {
            self::STATUS_ASSIGNED => self::STATUS_RESPONDING,
            self::STATUS_RESPONDING => self::STATUS_RESOLVED,
            self::STATUS_RESOLVED => self::STATUS_CLOSED,
            default => null,
        };
    }
}
