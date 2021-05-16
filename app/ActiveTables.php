<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ActiveTables extends Model
{

    protected $primaryKey = 'mesa_id';//llave primaria

    protected $fillable = [
        'mesa_id', 'user_id','invoice','delivery'//datos requeridos debe introducir el ususario
    ];

    protected $casts = [ //conversion de datos
        'products' => 'array',
        'delivery' => 'boolean',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
    

    protected $hidden = [ //no se envian o muestran estos datos
        'created_at', 'updated_at',
    ];

    //relaciones con otros modelos
    public function mesa()
    {
        return $this->belongsTo(Mesa::class);
    }

    public function waiter()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'active_products','active_id');
    }
}
