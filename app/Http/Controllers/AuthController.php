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
    public function signUp(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'Successfully created user!'
        ], 201);
    }

    public function send_reset_pass(Request $request)
    {
        # code...
        $user = User::where('email', $request->email)->first();
        if ( !$user ) return redirect()->back()->withErrors(['error' => '404']);

        $code = Str::random(10);

        $app_code = DB::table('app_password_resets')->where('email',$request->email)->delete();
        
        DB::table('app_password_resets')->insert([
            'email' => $request->email,
            'code' => $code, 
            'created_at' => Carbon::now()
        ]);

        Mail::to($user->email)->send(new ResetPasswordCode($code));
        
        return response()->json(['message'=>'Se ha enviado el correo para restablecer su contraseña']);
    }
    
    public function code_pass(Request $request)
    {
        $app_code = DB::table('app_password_resets')->where('code',$request->code)->first();
        if( !$app_code )
            return response()->json(['No exite código']);
        else
            return response()->json(['Se encontro el código']);
    }

    public function change_password(Request $request)
    {
        $app_code = DB::table('app_password_resets')->where('email',$request->email)->where('code',$request->code)->first();
        if($app_code)
        {
            if($request->password == $request->repeat_password)
            {
                DB::table('users')
                    ->where('email',$request->email)
                    ->update([
                        'password'=> Hash::make($request->password)
                    ]);
                DB::table('app_password_resets')->where('email',$request->email)->delete();
                return response()->json(['Se ha cambiado tu contraseña']);
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
        ]);

        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');

        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        
        $user->getRoleNames();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString(),
        ]);
    }

    /**
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Obtener el objeto User como json
     */
    public function user()
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
