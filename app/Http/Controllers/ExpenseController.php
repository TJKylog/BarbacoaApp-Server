<?php

namespace App\Http\Controllers;

use App\Expense;
use App\Ticket;
use App\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    /**
     * Se evian todos los egresos
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
     * 
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Se guarga un nuevo 
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //se validan los datos del usuario
        $request->validate([
            'approved_by' => 'required|string|max:150',
            'reason' => 'required|string|max:255',
            'amount' => 'required|between:0,99999.99',
        ]);

        //se obtienes todos los egresos, tickets y enventos para hacer la validación
        $expenses = Expense::whereDate('created_at', Carbon::today())->get();
        $tickets = Ticket::whereDate('created_at', Carbon::today())->get();
        $events = Event::whereDate('created_at', Carbon::today())->where('is_completed',true)->get();


        $totalCash = 0;
        $totalExpenses = 0;
        $totalEvents = 0;

        foreach($expenses as $expense) {
            $totalExpenses = $totalExpenses + $expense->amount;
        }

        foreach($tickets as $ticket)
        {
            if($ticket->purchase_info['payment_method'] == 'Efectivo')
            {
                $totalCash = $totalCash + $ticket->purchase_info['total'];
            }
        }

        foreach($events as $event){
            $totalEvents = $totalEvents + $event->event_info['advance_payment'];
        }

        $sales_day = ($totalCash + $totalEvents) - $totalExpenses;

        if($request->amount > $sales_day)
        {
            return response()->json([
                "message" => "El egreso debe ser menor a $ ".number_format((float)$sales_day, 2, '.', '')
            ],200);// si el egreso es mayor a la venta del día se mandara un mensaje de que no debe ser menor a la catidad dada
        }
        else {
            $expense = Expense::create([
                'approved_by' => $request->approved_by,
                'reason' => $request->reason,
                'amount' => $request->amount,
                'created_by' => Auth::user()->id
            ]);// se crea el egreso

            return response()->json([
                "message" => "Se guardo correctamente el egreso "
            ],200); // se envia un mensaje de que el egreso de guardo correctamente
        }
    }

    /**
     * Se obtiene los datos del egreso encontrado
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
     * Se actualizan los datos de un egreso.
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
        ]);//se validan los datos

        //se obtienen todos los egresos, tickets y eventos para hacer la validación de la venta del día

        $expenses = Expense::whereDate('created_at', Carbon::today())->get();
        $tickets = Ticket::whereDate('created_at', Carbon::today())->get();
        $events = Event::whereDate('created_at', Carbon::today())->where('is_completed',true)->get();


        $totalCash = 0;
        $totalExpenses = 0;
        $totalEvents = 0;

        foreach($expenses as $expense) {
            $totalExpenses = $totalExpenses + $expense->amount;
        }

        foreach($tickets as $ticket)
        {
            if($ticket->purchase_info['payment_method'] == 'Efectivo')
            {
                $totalCash = $totalCash + $ticket->purchase_info['total'];
            }
        }

        foreach($events as $event){
            $totalEvents = $totalEvents + $event->event_info['advance_payment'];
        }

        $sales_day = ($totalCash + $totalEvents) - $totalExpenses;

        if($request->amount > $sales_day)
        {
            return response()->json([
                "message" => "El egreso debe ser menor a $ ".number_format((float)$sales_day, 2, '.', '')
            ],200);
        }
        else {
            $expense = Expense::where('id',$id)->first();
            $expense->approved_by = $request->approved_by;
            $expense->reason = $request->reason;
            $expense->amount = $request->amount;
            $expense->save();//se guardan los datos ingresados por el usuario

            return response()->json([
                "message" => "Se guardo correctamente el egreso "
            ],200);//manda mensaje de que el egreso se guardo correctamente
        }
    }

    /**
     * Se elimina un egreso.
     *
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //se busca el egreso
        $expense = Expense::where('id',$id)->with('user')->first();
        $expense->delete();//se borra el egreso
        return response()->json(['message' => 'Egreso eliminado']);//se envia un mensaje de egreso eliminado
    }
}
