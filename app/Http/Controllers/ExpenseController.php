<?php

namespace App\Http\Controllers;

use App\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $expenses = Expense::with('user')->get()->makeHidden(['user']);
        foreach($expenses as $expense) {
            $expense->setAttribute('created_by_name', $expense->user->name);
        }
        return $expenses;
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
            'approved_by' => 'required|string|max:150',
            'reason' => 'required|string|max:255',
            'amount' => 'required|between:0,99999.99',
        ]);

        $expense = Expense::create([
            'approved_by' => $request->approved_by,
            'reason' => $request->reason,
            'amount' => $request->amount,
            'created_by' => Auth::user()->id
        ]);

        return response()->json($expense,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $expense = Expense::where('id',$id)->with('user')->first()->makeHidden(['user']);
        $expense->setAttribute('created_by_name', $expense->user->name);
        return $expense;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function edit(Expense $expense)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'approved_by' => 'required|string|max:150',
            'reason' => 'required|string|max:255',
            'amount' => 'required|between:0,99999.99',
        ]);

        $expense = Expense::where('id',$id)->first();
        $expense->approved_by = $request->approved_by;
        $expense->reason = $request->reason;
        $expense->amount = $request->amount;
        $expense->save();

        return $expense;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $expense = Expense::where('id',$id)->with('user')->first();
        $expense->delete();
        return response()->json(['message' => 'Egreso eliminado']);
    }
}
