<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Official extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'position',
        'contact_number',
        'term_start',
        'term_end',
    ];

    protected function casts(): array
    {
        return [
            'term_start' => 'date',
            'term_end'   => 'date',
        ];
    }
}