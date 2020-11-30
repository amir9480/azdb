@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Manage staff') }}</div>

                    <div class="card-body text-center">
                        @include('components.success')
                        @include('components.errors')
                        <form method="POST" action="{{ route('buisnesses.staff.store', ['buisness' => $buisness]) }}">
                            @csrf
                            <div class="input-group mb-3">
                                <input type="email" name="email" class="form-control" placeholder="{{ __('User\'s email') }}" value="{{ old('email') }}">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-success">{{ __('Add') }}</button>
                                </div>
                            </div>
                        </form>
                        @if ($users->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('First Name') }}</th>
                                            <th>{{ __('Last Name') }}</th>
                                            <th>{{ __('Email') }}</th>
                                            <th>{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $user)
                                            <tr>
                                                <td>{{ $user->id }}</td>
                                                <td>{{ $user->first_name }}</td>
                                                <td>{{ $user->last_name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <form
                                                        action="{{ route('buisnesses.staff.destroy', ['buisness' => $buisness]) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('{{ __('Are you sure?') }}');"
                                                        >
                                                            @csrf
                                                            @method('DELETE')
                                                            <input type="hidden" name="user" value="{{ $user->id }}">
                                                            <button class="btn btn-danger">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info text-center">
                                {{ __('No staff') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
