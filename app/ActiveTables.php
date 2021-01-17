<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActiveTables extends Model
{

    protected $primaryKey = 'mesa_id';

    protected $fillable = [
        'mesa_id', 'user_id',
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

    public function products()
    {
        return $this->belongsToMany(Product::class, 'active_products');
    }
}
