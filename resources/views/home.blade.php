@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Reportes') }}</div>

                <div class="card-body">

                    <table class="table table-dark">
                        <thead>
                          <tr>
                            <th scope="col">#</th>
                            <th scope="col">info</th>
                          </tr>
                        </thead>
                        <tbody>
                            @foreach ($tickets as $item)
                                <tr>
                                    <td>
                                        {{$item->id}}
                                    </td>
                                    <td>
                                        @foreach ($item->purchase_info['consumes'] as $item)
                                            <p>
                                            @foreach ($item as $key => $value)
                                                {{$value}}
                                            @endforeach
                                            </p>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

