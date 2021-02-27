<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserLastname extends Model
{

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_id', 'first_lastname', 'second_lastname',
    ];

    public $timestamps = false;
}
