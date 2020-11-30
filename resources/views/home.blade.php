@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    {{ __('You are logged in!') }}
                    @if($yourBuisnesses->count() > 0)
                        <ul class="list-group mt-4">
                            <li class="list-group-item active">
                                {{ __('Your buisnesses') }}
                            </li>
                            @foreach ($yourBuisnesses as $buisness)
                                <li class="list-group-item">
                                    <a href="{{ route('buisnesses.show', ['buisness' => $buisness]) }}">{{ $buisness->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    <ul class="list-group mt-4">
                        @foreach ($buisnesses as $buisness)
                            <li class="list-group-item">
                                <a href="{{ $buisness->link }}">{{ $buisness->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
