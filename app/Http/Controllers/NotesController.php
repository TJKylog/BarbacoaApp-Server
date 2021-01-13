<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ActiveTables;
use App\Mesa;
use App\User;

class NotesController extends Controller
{
    //
    public function get_active()
    {
        return Mesa::select('mesas.*')
            ->join('active_tables','active_tables.mesa_id','=','mesas.id')
            ->get();
    }

    public function add_active(Request $request)
    {
        $active = ActiveTables::create([
            'user_id' => $request->user_id,
            'mesa_id' => $request->mesa_id
        ]);
        return $active;
    }

    public function get_available_info()
    {
        $mesas = Mesa::select('mesas.*')
            ->leftJoin('active_tables','active_tables.mesa_id','=','mesas.id')
            ->where('active_tables.id',NULL)
            ->get();
        $users = User::select('id','name')->role('normal')->get();

        return response()->json([
            'mesas' => $mesas,
            'waiters' => $users
        ]);
    }

    public function add_product()
    {
        
    }

    public function delete_product()
    {
        
    }
    public function update_unit_product()
    {
        
    }
}
