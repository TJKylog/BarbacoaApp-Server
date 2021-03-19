<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $products = Product::get();
        return response()->json($products);
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
        $request->validate([
            'name' => 'unique:products|required|string|max:100',
            'price' => 'required|between:0,999999.99',
            'measure' => 'required|string|max:50',
            'type' => 'required|string|max:50'
        ]);

        return response()->json(Product::create($request->all()));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Product::where('id',$id)->first();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|between:0,999999.99',
            'measure' => 'required|string|max:50',
            'type' => 'required|string|max:50'
        ]);

        $product = Product::where('id',$id)->first();
        $product->name = $request->name;
        $product->price = $request->price;
        $product->measure = $request->measure;
        $product->type = $request->type;
        $product->save();

        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $product = Product::where('id',$id)->first();
        $name = $product->name;
        $product->delete();
        return response()->json(['message' => $name.' eliminado correctamente']);
    }

    public function get_type_produts()
    {
        $type = Product::select('type')->distinct()->get();
        return $type;
    }

    public function get_products_by_type($type)
    {
        $products = Product::where('type',$type)->get();
        return $products;
    }
    
    public function validate_name(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100'
        ]);

        $product = Product::where('name',$request->name)->first();

        if(isset($product))
            return response()->json([ 'exist' => true]);
        else
            return response()->json([ 'exist' => false]);
    }
}
