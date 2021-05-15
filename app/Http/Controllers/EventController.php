<?php

namespace App\Http\Controllers;

use App\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    /**
     * Función que consulta todos los eventos en la BD y los envia en formato JSON.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() /* Se envian todos los eventos */
    {
        //
        $events = Event::get();
        return response()->json($events);
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
     * Guarda un nuevo evento y retorna el evento creado en formato JSON.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $event =  Event::create($request->all());
        if($request->event_info['total'] == $request->event_info['advance_payment'])
        {
            DB::table('events')->where('id', $event->id)->update(['is_completed' => true ]);
        }

        return response()->json($event);
    }

    /**
     * Buscar en la base el id del evento y retorna con formato JSON.
     *
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $event = Event::where('id',$id)->first();
        return response()->json($event);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Fucion para hacer actualizacion de datos de un evento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        //
        
        $event = Event::where('id',$id)->first();
        $event->event_info = $request->event_info;
        if($request->event_info['total'] == $request->event_info['advance_payment'])
        {
            $event->is_completed = true;
        }
        else{
            $event->is_completed = false;
        }
        $event->save();

        return response()->json($event);
    }

    /**
     * Elimina un evento.
     *
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $event = Event::where('id',$id)->first();
        $event->delete();
        return response()->json(['message' => ' eliminado correctamente']);
    }
}
