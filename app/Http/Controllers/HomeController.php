<?php

namespace App\Http\Controllers;

use App\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    public function index()
    {
        $tickets = Ticket::get();
        return view('home',compact('tickets'));
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
}
