<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'is_completed', 'event_info',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'event_info' => 'array',//se guarda la info del evento
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
}
