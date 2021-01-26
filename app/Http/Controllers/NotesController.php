<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function delete_active($id)
    {
        $active = ActiveTables::where('mesa_id',$id)->first();
        $active->delete();
        return response()->json([
            'message' => 'Mesa borrada'
        ]);
    }

    public function get_available_info()
    {
        $mesas = Mesa::select('mesas.*')
            ->leftJoin('active_tables','active_tables.mesa_id','=','mesas.id')
            ->where('active_tables.mesa_id',NULL)
            ->get();
        $users = User::select('id','name')->role('normal')->get();

        return response()->json([
            'mesas' => $mesas,
            'waiters' => $users
        ]);
    }

    public function update_product(Request $request, $id)
    {
        /*
            Data request structure
            {
                product_id: data,
                amount: data
            }
        */
        $active = ActiveTables::where('mesa_id',$id)->first();
        if(DB::table('active_products')->where('active_id',$active->mesa_id)->where('product_id',$request->product_id)->first()) {
            DB::table('active_products')
                ->where('active_id',$active->mesa_id)
                ->where('product_id',$request->product_id)
                ->update([
                    'amount' => $request->amount
                ]);
        } else {
            $active->products()->attach([$request->product_id => ['amount' => $request->amount]]);
        }
        
        return $active;
    }

    public function delete_product(Request $request, $id)
    {
        $active = ActiveTables::where('mesa_id',$id)->first();
        $active->products()->detach([$request->product_id]);
    }
}
