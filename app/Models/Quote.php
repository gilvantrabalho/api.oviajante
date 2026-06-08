<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $fillable = [
        'destino',
        'data_inicio',
        'data_fim',
        'request_payload',
        'response_payload',
        'total_final',
    ];

    protected function casts(): array
    {
        return [
            'data_inicio' => 'date',
            'data_fim' => 'date',
            'request_payload' => 'array',
            'response_payload' => 'array',
            'total_final' => 'float',
        ];
    }
}
