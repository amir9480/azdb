@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <div>{{ __('Your tickets to :buisness', ['buisness' => $buisness->name]) }}</div>
                        <div>
                            <a href="{{ route('tickets.create', ['buisness' => $buisness]) }}" class="btn btn-success float-left">{{ __('Create') }}</a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if ($tickets->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Subject</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tickets as $ticket)
                                        <tr>
                                            <td>{{ $ticket->id }}</td>
                                            <td>{{ $ticket->subject }}</td>
                                            <td>
                                                {{ $ticket->status_localed }}
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('tickets.show', ['buisness' => $buisness, 'ticket' => $ticket]) }}" class="btn btn-primary">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            {{ __('No Tickets') }}
                            <br>
                            <a href="{{ route('tickets.create', ['buisness' => $buisness]) }}" class="btn btn-success mt-3">{{ __('Create first') }}</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
