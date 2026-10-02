<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'resident_id',
        'certificate_type',
        'purpose',
        'fee',
        'date_issued',
    ];

    protected function casts(): array
    {
        return [
            'date_issued' => 'date',
            'fee' => 'decimal:2',
        ];
    }

    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }
}
