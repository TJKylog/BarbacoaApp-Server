<?php

namespace App\Http\Controllers;

use App\Ticket;
use App\Expense;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TicketController extends Controller
{
    /**
     * Envia todos tickets
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return response()->json(Ticket::get());
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
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function show(Ticket $ticket)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function edit(Ticket $ticket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ticket $ticket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ticket $ticket)
    {
        //
    }
    
    /* Venta del día */
    public function sale_day()
    {
        $expenses = Expense::whereDate('created_at', Carbon::today())->get();
        $tickets = Ticket::whereDate('created_at', Carbon::today())->get();
        $totalCard = 0;
        $totalCash = 0;
        $totalExpenses = 0;
        foreach($expenses as $expense) {
            $totalExpenses = $totalExpenses + $expense->amount;
        }
        foreach($tickets as $ticket)
        {
            if($ticket->purchase_info['payment_method'] == 'Tarjeta')
            {
                $totalCard = $totalCard + $ticket->purchase_info['total'];
            }
            if($ticket->purchase_info['payment_method'] == 'Efectivo')
            {
                $totalCash = $totalCash + $ticket->purchase_info['total'];
            }
        }
        return response()->json(['total' => ($totalCash-$totalExpenses) ]);
    }
}
