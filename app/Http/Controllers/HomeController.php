<?php

namespace App\Http\Controllers;

use App\Event;
use App\Expense;
use App\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['root']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $query = "";
        if($request->query('day')){

            $expenses = Expense::whereDate('created_at',$request->query('day') )->get();
            $tickets = Ticket::whereDate('created_at', $request->query('day'))->get();
            $events = Event::whereDate('created_at', $request->query('day'))->where('is_completed',true)->get();
            $query = "?day=".$request->query('day');
        }
        else if($request->query('month'))
        {
            $temp = explode('-',$request->query('month'));
            $year = $temp[0];
            $month = $temp[1];
            $expenses = Expense::whereYear('created_at', '=', $year)
            ->whereMonth('created_at', '=', $month)->get();
            $tickets = Ticket::whereYear('created_at', '=', $year)
            ->whereMonth('created_at', '=', $month)->get();
            $events = Event::whereYear('created_at', '=', $year)
            ->whereMonth('created_at', '=', $month)->where('is_completed',true)->get();
            $query = "?month=".$request->query('month');
        }
        else if($request->query('year'))
        {
            $expenses = Expense::whereYear('created_at', '=', $request->query('year'))->get();
            $tickets = Ticket::whereYear('created_at', '=', $request->query('year'))->get();
            $events = Event::whereYear('created_at', '=', $request->query('year'))->where('is_completed',true)->get();
            $query = "?year=".$request->query('year');
        }
        else {
            $expenses = Expense::whereDate('created_at', Carbon::today())->get();
            $tickets = Ticket::whereDate('created_at', Carbon::today())->get();
            $events = Event::whereDate('created_at', Carbon::today())->where('is_completed',true)->get();
        }

        $totalCard = 0;
        $totalCash = 0;
        $totalExpenses = 0;
        $totalEvents = 0;

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

        foreach($events as $event){
            $totalEvents = $totalEvents + $event->event_info['total'];
        }

        return view('home',compact('tickets','totalCard','totalCash','expenses','totalExpenses','query','events','totalEvents'));
    }

    public function root()
    {
        if(Auth::check()){
            return redirect()->route('home');
        }
        else{
            return view('welcome');
        }
    }

    public function download(Request $request)
    {
        $report_name = "";
        if($request->query('day')){

            $expenses = Expense::whereDate('created_at',$request->query('day') )->get();
            $tickets = Ticket::whereDate('created_at', $request->query('day'))->get();
            $events = Event::whereDate('created_at', $request->query('day'))->where('is_completed',true)->get();
            $report_name = "Reporte ".$request->query('day');
        }
        else if($request->query('month'))
        {
            $temp = explode('-',$request->query('month'));
            $year = $temp[0];
            $month = $temp[1];
            $expenses = Expense::whereYear('created_at', '=', $year)
            ->whereMonth('created_at', '=', $month)->get();
            $tickets = Ticket::whereYear('created_at', '=', $year)
            ->whereMonth('created_at', '=', $month)->get();
            $events = Event::whereYear('created_at', '=', $year)
            ->whereMonth('created_at', '=', $month)->where('is_completed',true)->get();
            $report_name = "Reporte ".$request->query('month');
        }
        else if($request->query('year'))
        {
            $expenses = Expense::whereYear('created_at', '=', $request->query('year'))->get();
            $tickets = Ticket::whereYear('created_at', '=', $request->query('year'))->get();
            $events = Event::whereYear('created_at', '=', $request->query('year'))->where('is_completed',true)->get();
            $report_name = "Reporte ".$request->query('year');
        }
        else {
            $expenses = Expense::whereDate('created_at', Carbon::today())->get();
            $tickets = Ticket::whereDate('created_at', Carbon::today())->get();
            $events = Event::whereDate('created_at', Carbon::today())->where('is_completed',true)->get();
            $report_name = "Reporte ".Carbon::today();
        }

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

        $view =  View::make('reports.report',compact('tickets','totalCard','totalCash','expenses','totalExpenses'))->render();
        $pdf = \App::make('dompdf.wrapper');

        return $pdf->loadHTML($view)
            ->download($report_name.'.pdf');
    }
}
