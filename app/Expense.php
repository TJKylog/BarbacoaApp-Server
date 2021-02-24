<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'reason','approved_by','amount','created_by'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $hidden = [
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'created_by');
    }
}
