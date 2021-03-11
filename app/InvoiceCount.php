<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceCount extends Model
{
    protected $fillable = [
        'invoice_count',
    ];
    
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $hidden = [
        'created_at', 'updated_at',
    ];
}
