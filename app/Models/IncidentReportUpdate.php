<?php

namespace App\Models;

use Database\Factories\IncidentReportUpdateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidentReportUpdate extends Model
{
    /** @use HasFactory<IncidentReportUpdateFactory> */
    use HasFactory;

    protected $fillable = ['incident_report_id', 'user_id', 'status', 'message'];

    public function report(): BelongsTo
    {
        return $this->belongsTo(IncidentReport::class, 'incident_report_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
