<?php

namespace App\Models;

use Database\Factories\BlotterUpdateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlotterUpdate extends Model
{
    /** @use HasFactory<BlotterUpdateFactory> */
    use HasFactory;

    protected $fillable = ['blotter_id', 'user_id', 'status', 'message'];

    public function blotter(): BelongsTo
    {
        return $this->belongsTo(Blotter::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
