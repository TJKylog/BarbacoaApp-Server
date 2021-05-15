<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordCode;

class AuthController extends Controller
{
    /* 
        Envia el codigo de reseteo de contraseña
     */

    public function send_reset_pass(Request $request)
    {
        # 
        $user = User::where('email', $request->email)->first();
        if ( !$user ) return response()->json(['message' => 'el correo no existe'],404); //si el usuario no existe envia un mensaje de que el correo no existe

        if($user->hasRole('Mesero')) return response()->json(['message' => 'No tienes permitido restablecer contraseña'],403); // si el usuario es mesero envia un mesaje de que no puede cambiar la contraseña

        $code = Str::random(10); //se genera el codigo de restablecer la contraseña

        $app_code = DB::table('app_password_resets')->where('email',$request->email)->delete();//se borran todos los codigos del correo
        
        DB::table('app_password_resets')->insert([
            'email' => $request->email,
            'code' => $code, 
            'created_at' => Carbon::now()
        ]);//se guarda el correo con el correo y el codigo

        Mail::to($user->email)->send(new ResetPasswordCode($code)); //se envia el correo
        
        return response()->json(['message'=>'Se ha enviado el correo para restablecer su contraseña'],200);
    }
    
    public function code_pass(Request $request) //esta funcion solo se encarga de verificar si existe el codigo
    {
        $app_code = DB::table('app_password_resets')->where('code',$request->code)->first();
        if( !$app_code )
            return response()->json(['No exite código']);
        else
            return response()->json(['Se encontro el código']);
    }

    public function change_password(Request $request) //se verifica y cambia la contraseña
    {
        $app_code = DB::table('app_password_resets')->where('email',$request->email)->where('code',$request->code)->first();//verifica que correo conicida con la contraseña
        if($app_code)
        {
            if($request->password == $request->repeat_password) //verifica que las contraseñas coincidan
            {
                DB::table('users')
                    ->where('email',$request->email)
                    ->update([
                        'password'=> Hash::make($request->password)
                    ]);//se actualiza la contraseña
                DB::table('app_password_resets')->where('email',$request->email)->delete(); //se borra el codigo
                return response()->json(['Se ha cambiado tu contraseña'],200);
            }
        }
    }

    /**
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);// valida la info que escribio el usuario

        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Correo o contraseña incorrectos'
            ], 401);// muestra error si el usuario o la contraseña son incorrectos

        $user = $request->user();

        if($user->hasRole('Mesero'))
            return response()->json([
                'message' => 'No tienes permiso para entrar a la app'
            ], 402);// si el usuario es mesero envia un mensaje que no tiene permiso de usar la app

        $tokenResult = $user->createToken('Personal Access Token'); //se crea el token

        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1); //se añade una fecha de expiracion al token
        $token->save();
        
        $user->getRoleNames();

        return response()->json([ 
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString(),
        ]);// se envia el token, tipo de token y la fecha de exppiracion del token
    }

    /**
     */
    public function logout(Request $request) // cierra sesión 
    {
        $request->user()->token()->revoke();// se elimina el token de la sesión

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Obtener el objeto User como json
     */
    public function user() //se obtiene el usuario logeado
    {
        $user = User::where('id',Auth::user()->id)->with('roles','lastname')->first();
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'first_lastname' => $user->lastname->first_lastname,
            'second_lastname' => $user->lastname->second_lastname,
            'email' => $user->email,
            'role' => $user->roles[0]->name,
            'role_id' => $user->roles[0]->id
        ]);
    }
}
