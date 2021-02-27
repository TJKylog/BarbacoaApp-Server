<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\ActiveTables;
use App\Product;
use App\Ticket;
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
        $users = User::select('id','name')->role('Mesero')->get();

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
        $amount = 0;
        $active = ActiveTables::where('mesa_id',$id)->first();
        $product = Product::where('id',$request->product_id)->first();
        if(isset($product) && $product->measure == "Gramos")
        {
            $amount = $request->amount * 0.001;
        }
        else {
            $amount = $request->amount;
        }
        if(DB::table('active_products')->where('active_id',$active->mesa_id)->where('product_id',$request->product_id)->first()) {
            DB::table('active_products')
                ->where('active_id',$active->mesa_id)
                ->where('product_id',$request->product_id)
                ->update([
                    'amount' => $amount
                ]);
        } else {
            $active->products()->attach([$request->product_id => ['amount' => $amount]]);
        }
        
        return $active;
    }

    public function delete_product(Request $request, $id)
    {
        $active = ActiveTables::where('mesa_id',$id)->first();
        $active->products()->detach([$request->product_id]);
    }

    public function save_ticket(Request $request, $id)
    {

        $request->validate([
            'payment' => 'required|between:0,99999.99',
            'payment_method' => 'required|string|max:20',
        ]);

        $total = 0;
        $mesa = Mesa::where('id',$id)->first();
        $waiter = User::select('id','name')
            ->join('active_tables','active_tables.user_id','=','users.id')
            ->where('active_tables.mesa_id',$id)
            ->first();
        $products =  Product::
                select('products.id as id','products.name as name','products.measure','products.price','active_products.amount')
                ->join('active_products','active_products.product_id','=','products.id')
                ->where('active_products.active_id',$id)
                ->get();
        if(count($products) >= 1) {
            foreach($products as $item) {
                $amount_price = $item->amount * $item->price;
                $item->setAttribute('amount_price',number_format((float)$amount_price, 2, '.', ''));
                $total = $total + $amount_price; 
            }

            if($request->payment_method == "Tarjeta") {
                $mesa->setAttribute('waiter',$waiter);
                $mesa->setAttribute('consumes',$products);
                $mesa->setAttribute('total',number_format((float)$total, 2, '.', ''));

                $mesa->setAttribute('payment_method',$request->payment_method);
                $mesa->setAttribute('payment',number_format((float)$total, 2, '.', ''));
                $mesa->setAttribute('change',0);

                
                $ticket = new Ticket;
                $ticket->purchase_info = $mesa;
                $ticket->save();

                $active = ActiveTables::where('mesa_id',$id)->delete();

                return response()->json(['message' => 'Se guardó correctamente la compra']);
            }
            else if($request->payment_method == "Efectivo") {

                if($request->payment >= $total)
                {
                    $change = $request->payment -$total;

                    $mesa->setAttribute('waiter',$waiter);
                    $mesa->setAttribute('consumes',$products);
                    $mesa->setAttribute('total',number_format((float)$total, 2, '.', ''));

                    $mesa->setAttribute('payment_method',$request->payment_method);
                    $mesa->setAttribute('payment',$request->payment);
                    $mesa->setAttribute('change',$change);

                    
                    $ticket = new Ticket;
                    $ticket->purchase_info = $mesa;
                    $ticket->save();

                    $active = ActiveTables::where('mesa_id',$id)->delete();

                    return response()->json(['message' => 'Se guardó correctamente la compra']);
                }
                else {
                    return response()->json(['message' => 'El pago no es sufiente para pagar el ticket']);
                }
            }
            else {
                return response()->json(['message' => 'No especifico el metodo de pago']);
            }
        }
        else {
            return response()->json(['message' => 'La mesa no tiene productos']);
        }
    }
}
