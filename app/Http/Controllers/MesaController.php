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
     * Se envian todas las mesas ordenados por nombre.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Mesa::orderBy('name')->get());
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
     * Se guarda la mesa.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'name' => 'required|unique:mesas|string|max:100'// se valida el nombre de la mesa se unico
        ]);

        return response()->json(Mesa::create($request->all()));
    }

    /**
     * Se envian los datos de la mesa activa con los articulos, mesero y el total del consumo.
     *
     * @param  \App\Mesa  $mesa
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $total = 0;
        $mesa = Mesa::where('id',$id)->with('active')
            ->first()->makeHidden(['active']);//busca si la mesa esta activa
        if(isset($mesa->active))
        {
            $mesa->setAttribute('delivery',$mesa->active->delivery);//Se añade la variable que sirve  para identificar si es para llevar o para comedor
            $mesa->setAttribute('invoice',$mesa->active->invoice);//Se añade la variable que sirve para identificar si ya tiene un folio asignado
        }
        $waiter = User::select('id','name')
            ->join('active_tables','active_tables.user_id','=','users.id')
            ->where('active_tables.mesa_id',$id)
            ->first();//
        $products =  Product::
                select('products.id as id','products.name as name','products.measure','products.price','active_products.amount')
                ->join('active_products','active_products.product_id','=','products.id')
                ->where('active_products.active_id',$id)
                ->get();//
        
        foreach($products as $item)
        {
            $amount_price = $item->amount * $item->price;
            $item->setAttribute('amount_price',number_format((float)$amount_price, 2, '.', ''));
            $total = $total + $amount_price; 
        }
        $mesa->setAttribute('waiter',$waiter);// se añade el id y el nombre del mesero
        $mesa->setAttribute('consumes',$products);// se añade el id, nombre, precio , catidad consumida de cada producto
        $mesa->setAttribute('total',number_format((float)$total, 2, '.', ''));
        return $mesa;
    }

    /**
     * Se obtiene la mesa que se desea editar
     *
     * @param  \App\Mesa  $mesa
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return Mesa::where('id',$id)->first();//
    }

    /**
     * Se actualiza una mesa
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Mesa  $mesa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        //
        $request->validate([
            'name' => 'required|string|max:100'
        ]);

        $mesa = Mesa::where('id',$id)->first();
        $mesa->name = $request->name;
        $mesa->save();
 
        return response()->json($mesa);
    }

    /**
     * Elimina una mesa
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
