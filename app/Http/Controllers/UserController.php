<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\UserLastname;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $users = User::with('lastname','roles')->get();
        foreach($users as $user) {
            $user->setAttribute('role', $user->roles[0]->name);
            $user->setAttribute('role_id', $user->roles[0]->id);
            if(isset($user->lastname)) {
                $user->name = $user->name.' '.$user->lastname->first_lastname.' '.$user->lastname->second_lastname;
            }
        }

        return response()->json($users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        if($request->role == 'Mesero')
        {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|email|unique:users'
            ]);
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Str::random(20)
            ]);
        }
        else {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|string'
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
        }

        $lastname = UserLastname::create([
            'user_id' => $user->id,
            'first_lastname' => $request->first_lastname,
            'second_lastname' => $request->second_lastname
        ]);

        $user->assignRole($request->role);
        return response()->json($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::where('id',$id)->with('roles','lastname')->first()->makeHidden(['roles','email_verified_at','lastname']);
        if($user->lastname)
        {
            $user->setAttribute('first_lastname',$user->lastname->first_lastname);
            $user->setAttribute('second_lastname',$user->lastname->second_lastname);
        }
        else {
            $user->setAttribute('first_lastname',null);
            $user->setAttribute('second_lastname',null);
        }
        $user->setAttribute('role', $user->roles[0]->name);
        $user->setAttribute('role_id', $user->roles[0]->id);
        return $user;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $request->validate([
            'name' => 'required|string',
        ]);
        $user = User::where('id',$id)->first();
        $user->name = $request->name;
        $user->email = $request->email;
        if(isset($request->password))
        {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        $lastname = UserLastname::where('user_id',$id)->first();
        if(isset($lastname))
        {
            $lastname->first_lastname = $request->first_lastname;
            $lastname->second_lastname = $request->second_lastname;
            $lastname->save();
        }
        else {
            UserLastname::create([
                'user_id' => $user->id,
                'first_lastname' => $request->first_lastname,
                'second_lastname' => $request->second_lastname
            ]);
        }

        $user->syncRoles([$request->role]);
        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $user = User::where('id',$id)->first();
        $user->delete();
        return response()->json(['message' => 'Usuario eliminado']);
    }
}
