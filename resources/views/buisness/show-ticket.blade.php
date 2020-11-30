@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    {{ $ticket->subject }} - {{ $ticket->user->name }}
                </div>

                <div id="app" class="card-body">
                    <ticket-view
                        end-point="{{ route('buisnesses.ticket-show', ['buisness' => $buisness, 'ticket' => $ticket]) }}"
                        :user='@json(Auth::user())'
                        :ticket='@json(\App\Http\Resources\TicketResource::make($ticket))'
                        :support="true"
                    ></ticket-view>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
