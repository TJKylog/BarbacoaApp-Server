<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\ActiveTables;
use App\InvoiceCount;
use App\Product;
use App\Ticket;
use App\Mesa;
use App\User;
use Carbon\Carbon;

class NotesController extends Controller
{
    // se envian las mesas que estan ocupadas
    public function get_active()
    {
        return Mesa::select('mesas.*')
            ->join('active_tables','active_tables.mesa_id','=','mesas.id')
            ->orderBy('name')
            ->get();
    }

    /* Se añade una mesa ocupada */
    public function add_active(Request $request)
    {
        $active = ActiveTables::create([
            'user_id' => $request->user_id,
            'mesa_id' => $request->mesa_id,
            'delivery' => $request->delivery
        ]);

        return $active;
    }

    /* Borra una mesa ocupada */
    public function delete_active($id)
    {
        $active = ActiveTables::where('mesa_id',$id)->first();
        $active->delete();
        return response()->json([
            'message' => 'Mesa borrada'
        ]);
    }

    /* Se envian las mesas disponibles y los meseros */
    public function get_available_info()
    {
        $mesas = Mesa::select('mesas.*')
            ->leftJoin('active_tables','active_tables.mesa_id','=','mesas.id')
            ->where('active_tables.mesa_id',NULL)
            ->get();
        $users = User::select('id','name')->with('lastname')->role('Mesero')->get()->makeHidden(['lastname']);
        foreach($users as $user) {
            if(isset($user->lastname)) {
                $user->name = $user->name.' '.$user->lastname->first_lastname.' '.$user->lastname->second_lastname;
            }
        }

        return response()->json([
            'mesas' => $mesas,
            'waiters' => $users
        ]);
    }

    /* Se actualiza la cantidad consumida de un articulo o añade un articulo a una mesa activa */
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

        //si ya existe el articulo en el consumo de la mesa son actualiza la catidad
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

    /* Elimina un articulo consumido de una mesa */
    public function delete_product(Request $request, $id)
    {
        $active = ActiveTables::where('mesa_id',$id)->first();
        $active->products()->detach([$request->product_id]);
    }

    /* Fija un folio a una mesa activa y envia el consumo y el mesero que atiende */
    public function set_invoice_note($id)
    {
        $active = ActiveTables::where('mesa_id',$id)->first();
        if($active->invoice == -1) {

            $count = 1;
            $invoice = InvoiceCount::whereDate( 'updated_at' , Carbon::today())->first();
            if(isset($invoice))
            {
                if($invoice->invoice_count <= 100 )
                {
                    $count = $invoice->invoice_count;
                    $invoice->invoice_count = $invoice->invoice_count + 1;
                }
                else {
                    $invoice->invoice_count = 2;
                }
            }
            else{
                $invoice = InvoiceCount::where('id',1)->first();
                $invoice->invoice_count = 2;
            }
            $invoice->save();

            ActiveTables::where('mesa_id',$id)->update([
                'invoice' => $count
            ]);

        }

        $total = 0;
        $mesa = Mesa::where('id',$id)->with('active')
            ->first()->makeHidden(['active']);
        if(isset($mesa->active))
        {
            $mesa->setAttribute('delivery',$mesa->active->delivery);
            $mesa->setAttribute('invoice',$mesa->active->invoice);
        }
        $waiter = User::select('id','name')
            ->join('active_tables','active_tables.user_id','=','users.id')
            ->where('active_tables.mesa_id',$id)
            ->first();
        $products =  Product::
                select('products.id as id','products.name as name','products.measure','products.price','active_products.amount')
                ->join('active_products','active_products.product_id','=','products.id')
                ->where('active_products.active_id',$id)
                ->get();
        
        foreach($products as $item)
        {
            $amount_price = $item->amount * $item->price;
            $item->setAttribute('amount_price',number_format((float)$amount_price, 2, '.', ''));
            $total = $total + $amount_price; 
        }
        $mesa->setAttribute('waiter',$waiter);
        $mesa->setAttribute('consumes',$products);
        $mesa->setAttribute('total',number_format((float)$total, 2, '.', ''));
        return $mesa;

    }

    /* Se guarda el ticket con el metodo de pago, folio, consumo, total pagado, mesero que atendio y el cambio */
    public function save_ticket(Request $request, $id)
    {
        $active = ActiveTables::where('mesa_id',$id)->first();
        if($active->invoice == -1) {
            return response()->json(['message' => 'Aun no tiene folio'],403);   
        }

        $request->validate([
            'payment' => 'required|between:0,99999.99',
            'payment_method' => 'required|string|max:20',
        ]);

        $total = 0;
        $mesa = Mesa::where('id',$id)->with('active')->first()->makeHidden(['active']);
        $mesa->setAttribute('delivery',$mesa->active->delivery);
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
                $mesa->setAttribute('invoice',$mesa->active->invoice);
                $mesa->setAttribute('consumes',$products);
                $mesa->setAttribute('total',number_format((float)$total, 2, '.', ''));

                $mesa->setAttribute('payment_method',$request->payment_method);
                $mesa->setAttribute('payment',number_format((float)$total, 2, '.', ''));
                $mesa->setAttribute('change',0);

                $ticket = new Ticket;
                $ticket->purchase_info = $mesa;
                $ticket->save();

                $active = ActiveTables::where('mesa_id',$id)->delete();

                return response()->json(['message' => 'Se guardó correctamente la compra','invoice' => $mesa->active->invoice],200);
            }
            else if($request->payment_method == "Efectivo") {

                if($request->payment >= $total)
                {

                    $change = $request->payment -$total;

                    $mesa->setAttribute('waiter',$waiter);
                    $mesa->setAttribute('invoice',$mesa->active->invoice);
                    $mesa->setAttribute('consumes',$products);
                    $mesa->setAttribute('total',number_format((float)$total, 2, '.', ''));

                    $mesa->setAttribute('payment_method',$request->payment_method);
                    $mesa->setAttribute('payment',$request->payment);
                    $mesa->setAttribute('change',$change);
                    
                    $ticket = new Ticket;
                    $ticket->purchase_info = $mesa;
                    $ticket->save();

                    $active = ActiveTables::where('mesa_id',$id)->delete();

                    return response()->json(['message' => 'Se guardó correctamente la compra','invoice' => $mesa->active->invoice],200);
                }
                else {
                    return response()->json(['message' => 'El pago no es sufiente para pagar el ticket'],401);
                }
            }
            else {
                return response()->json(['message' => 'No especifico el metodo de pago'],402);
            }
        }
        else {
            return response()->json(['message' => 'La mesa no tiene productos'],403);
        }
    }
}
