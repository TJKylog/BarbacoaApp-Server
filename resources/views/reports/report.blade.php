<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte</title>
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body style="background-color: #fff">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div>
                    <div class="card-header"> <h4 class="text-center">{{ __('Reportes') }}</h4> </div>
    
                    <div class="card-body">
                        <p><b>Tarjetas:</b> ${{$totalCard}} </p>
                        <p><b>Efectivo:</b> ${{$totalCash}} </p>
                        <p><b>Egresos:</b> ${{$totalExpenses}}</p>
                        <p><b>Eventos:</b> ${{$totalEvents}}</p>
                        <p><b>Venta del día:</b> ${{ ($totalCard + $totalCash) - ($totalExpenses + $totalCard) }} </p>
                        <p><b>Total neto:</b> ${{$totalCard + $totalCash}} </p>
                        <table class="table ">
                            <caption>Ventas</caption>
                            <thead>
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">Lugar</th>
                                <th scope="col">Atendido por</th>
                                <th scope="col">Consumo</th>
                                <th scope="col">Total</th>
                                <th scope="col">Pago</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach ($tickets as $ticket)
                                    <tr>
                                        <td>
                                            {{$ticket->id}}
                                        </td>
                                        <td>
                                            @if ($ticket->purchase_info['delivery'])
                                                Domicilio
                                            @else
                                                Comedor
                                            @endif
                                        </td>
                                        <td>
                                            {{$ticket->purchase_info['waiter']['name']}}
                                        </td>
                                        <td>
                                            <div class="col">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                        <th scope="col">Producto</th>
                                                        <th scope="col">Catidad</th>
                                                        <th scope="col">Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($ticket->purchase_info['consumes'] as $item)
                                                            <tr>
                                                                <td>
                                                                    {{$item['name']}}
                                                                </td>
                                                                <td>
                                                                    @if(($item['measure'] == "Gramos") || $item['measure'] == "gramos")
                                                                        {{$item['amount'] * 1000 }} {{$item['measure'] }}
                                                                    @elseif($item['measure'] == "Dulcesito corazón")
                                                                        {{$item['amount']}}
                                                                    @else
                                                                        {{$item['amount']}} {{$item['measure'] }}
                                                                    @endif
                                                                    
                                                                </td>
                                                                <td>
                                                                    $ {{$item['amount_price']}}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                        <td>
                                            ${{$ticket->purchase_info['total']}}
                                        </td>
                                        <td>
                                            {{$ticket->purchase_info['payment_method']}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <table class="table ">
                            <caption>Egresos</caption>
                            <thead>
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">Autorizo</th>
                                <th scope="col">Motivo</th>
                                <th scope="col">Cantidad</th>
                                <th scope="col">Creado por</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach ($expenses as $expense)
                                    <tr>
                                       <td>{{$expense->id}}</td>
                                       <td>{{$expense->approved_by}}</td>
                                       <td>{{$expense->reason}}</td>
                                       <td>{{$expense->amount}}</td>
                                       <td>{{$expense->user->name}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <table class="table ">
                            <caption>Eventos</caption>
                            <thead>
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">Total</th>
                                <th scope="col">Productos</th>
                                <th scope="col">Fecha</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach ($events as $event)
                                    <tr>
                                       <td>{{$event->id}}</td>
                                       <td>{{$event->event_info['total']}}</td>
                                       <td>
                                        <div class="col">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                    <th scope="col">Producto</th>
                                                    <th scope="col">Catidad</th>
                                                    <th scope="col">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($event->event_info['basic_package'] as $item)
                                                        <tr>
                                                            <td>
                                                                {{$item['name']}}
                                                            </td>
                                                            <td>
                                                                {{$item['amount']}}
                                                            </td>
                                                            <td>
                                                                $ {{$item['price']}}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    @foreach ($event->event_info['extras_list'] as $item)
                                                    <tr>
                                                        <td>
                                                            {{$item['name']}}
                                                        </td>
                                                        <td>
                                                            {{$item['amount']}}
                                                        </td>
                                                        <td>
                                                            $ {{$item['price']}}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                       </td>
                                       <td>{{$event->event_info['date']}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>