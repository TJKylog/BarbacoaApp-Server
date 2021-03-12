@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 mb-4">
            <div class="row">
                <div class="col-4">
                    <form action="" method="get">
                        <div class="form-group">
                            <label for="">Buscar por día</label>
                            <input class="form-control" type="date" name="day" placeholder="Buscar día" required>
                        </div>
                        <button class="btn btn-primary">Buscar</button>
                    </form>
                </div>
                <div class="col-4">
                    <form action="" method="get">
                        <div class="form-group">
                            <label>Buscar por mes</label>
                            <input class="form-control" type="month" name="month" required>
                        </div>
                        <button class="btn btn-primary">Buscar</button>
                    </form>
                </div>
                <div class="col-4">
                    <form action="" placeholder="Buscar mes" method="get">
                        <div class="form-group">
                            <label>Buscar por año</label>
                            <input class="form-control" type="number" name="year" required placeholder="Año" min="2000" max="2099" >
                        </div>
                        <button class="btn btn-primary">Buscar</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12 text-center mb-4">
            <a href="{{route('index')}}/report{{$query}}" target="_blank" rel="noopener noreferrer">Descargar PDF</a>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"> <h4 class="text-center">{{ __('Reportes') }}</h4> </div>

                <div class="card-body">

                    <p><b>Tarjetas:</b> ${{$totalCard}} </p>
                    <p><b>Efectivo:</b> ${{$totalCash}} </p>
                    <p><b>Egresos:</b> ${{$totalExpenses}}</p>
                    <p><b>Venta del día:</b> ${{ $totalCash - ($totalCard + $totalExpenses )}} </p>
                    <p><b>Total neto:</b> ${{$totalCard + $totalCash}} </p>

                    <ul class="nav nav-pills d-flex justify-content-center mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="pills-sales-tab" data-toggle="pill" href="#pills-sales" role="tab" aria-controls="pills-sales" aria-selected="true">Ventas</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="pills-expenses-tab" data-toggle="pill" href="#pills-expenses" role="tab" aria-controls="pills-expenses" aria-selected="false">Egresos</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">Eventos</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-sales" role="tabpanel" aria-labelledby="pills-sales-tab">
                            <table class="table ">
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
                                                {{$ticket->id}} - Folio {{$ticket->purchase_info['invoice']}}
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
                        </div>
                        <div class="tab-pane fade" id="pills-expenses" role="tabpanel" aria-labelledby="pills-expenses-tab">
                            <table class="table ">
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
                        </div>
                        <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

