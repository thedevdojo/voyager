@extends('voyager::auth.master')

@section('content')
    <div class="login-container">
        <p>{{ __('Reset Password') }}</p>

        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        @include('voyager::alerts')

        <form method="POST" action="{{ route('voyager.password.email') }}">
            @csrf

            <div class="form-group form-group-default @error('email') has-error @enderror">
                <label for="email">{{ __('voyager::generic.email') }}</label>
                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
            </div>

            <button type="submit" class="btn btn-block login-button">
                {{ __('voyager::passwords.password_send') }}
            </button>
        </form>

        <div style="clear:both"></div>

        @if(!$errors->isEmpty())
            <div class="alert alert-red">
                <ul class="list-unstyled">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

    </div>
@endsection