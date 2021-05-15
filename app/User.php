<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\UserLastname;

class User extends Authenticatable // Authenticatable es para el modelo de usuario pueda iniciar sesión en la pagina web
{
    use Notifiable, HasApiTokens,HasRoles;
    /* Notifiable sirve para enviar los correos de restablecer contraseña
        HasApiTokens sirve para generar tokens a los usuarios y pueda inciar sesión en la aplicacion
        HasRoles hace los usuarios puedan tener un rol (Super Admin, Admin, invitado o mesero)*/

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];// se definen la propiedades que el usuario debe de llenar al crear o modificar un usuario

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','created_at', 'updated_at'
    ];//se ocultan las propiedades que los usuarios pueden ver

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function lastname()//Llama al modelo de los apellidos para mostralos
    {
        return $this->hasOne(UserLastname::class);
    }
}
