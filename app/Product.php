<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $fillable = [
        'name', 'price', 'measure','type'
    ];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $hidden = [
        'created_at', 'updated_at',
    ];

    public function active()
    {
        return $this->belongsToMany(ActiveTables::class, 'active_products','product_id');
    }
}
