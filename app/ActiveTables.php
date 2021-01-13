<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActiveTables extends Model
{
    protected $fillable = [
        'mesa_id', 'user_id', 'products',
    ];

    protected $casts = [
        'products' => 'array',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
    

    protected $hidden = [
        'created_at', 'updated_at',
    ];

    public function mesa()
    {
        return $this->belongsTo(Mesa::class);
    }

    public function waiter()
    {
        return $this->belongsTo(User::class);
    }
}
