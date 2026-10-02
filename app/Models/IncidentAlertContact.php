<?php

namespace App\Models;

use Database\Factories\IncidentAlertContactFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidentAlertContact extends Model
{
    /** @use HasFactory<IncidentAlertContactFactory> */
    use HasFactory;

    protected $fillable = [
        'team',
        'contact_name',
        'phone',
        'email',
        'sms_enabled',
        'email_enabled',
        'is_active',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'sms_enabled' => 'boolean',
            'email_enabled' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
