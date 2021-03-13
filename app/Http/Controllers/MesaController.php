<?php

namespace App\Http\Controllers;

use App\Mesa;
use App\Product;
use App\User;
use Exception;
use Illuminate\Http\Request;

class MesaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Mesa::get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return;
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
        $request->validate([
            'name' => 'required|unique:mesas|string|max:100'
        ]);

        return response()->json(Mesa::create($request->all()));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Mesa  $mesa
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Mesa  $mesa
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return Mesa::where('id',$id)->first();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Mesa  $mesa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        //
        $request->validate([
            'name' => 'required|unique:mesas|string|max:100'
        ]);

        $mesa = Mesa::where('id',$id)->first();
        $mesa->name = $request->name;
        $mesa->save();
 
        return response()->json($mesa);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Mesa  $mesa
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $mesa = Mesa::where('id',$id)->first();
        $name = $mesa->name;
        $mesa->delete();

        return response()->json(['message'=> $name.' eliminada correctamente']);
    }
}
