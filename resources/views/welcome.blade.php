@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Home') }}</div>

                    <div class="card-body text-center">
                        <h1>{{ __('Welcome to ticket project') }}</h1>
                    </div>
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <a href="{{ route('register') }}" class="btn btn-primary">{{ __('Register') }}</a>
                        <a href="{{ route('login') }}" class="btn btn-secondary">{{ __('Login') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
