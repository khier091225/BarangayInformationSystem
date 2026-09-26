<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Resident extends Model
{
    use HasFactory;

    protected $fillable = [
        'household_id',
        'first_name',
        'middle_name',
        'last_name',
        'birthdate',
        'gender',
        'civil_status',
        'address',
        'contact_number',
        'is_voter',
    ];

    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
            'is_voter' => 'boolean',
            'registration_code_expires_at' => 'datetime',
        ];
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public static function findAvailableRegistrationCode(string $code): ?self
    {
        $resident = static::query()
            ->where('registration_code_hash', hash('sha256', $code))
            ->lockForUpdate()
            ->first();

        if ($resident === null || $resident->registration_code_expires_at === null
            || $resident->registration_code_expires_at->isPast() || $resident->user()->exists()) {
            return null;
        }

        return $resident;
    }

    public function clearRegistrationCode(): void
    {
        $this->forceFill([
            'registration_code_hash' => null,
            'registration_code_expires_at' => null,
        ])->save();
    }

    public function household()
    {
        return $this->belongsTo(Household::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function getFullNameAttribute(): string
    {
        return implode(' ', array_filter(
            [$this->first_name, $this->middle_name, $this->last_name],
            fn (?string $name): bool => filled($name),
        ));
    }
}
