@extends('voyager::auth.master')

@section('content')
    <div class="login-container">

        @include('voyager::alerts')

        <p>{{ __('voyager::passwords.password_confirm') }}</p>

        <form method="POST" action="{{ route('voyager.password.update') }}">
            @csrf

            <div class="form-group form-group-default @error('password') has-error @enderror">
                <label for="password">{{ __('voyager::passwords.password') }}</label>
                <input id="password" type="password" class="form-control" name="password" required min="8" autocomplete="current-password">
            </div>

            <button type="submit" class="btn btn-block login-button">
                {{ __('voyager::passwords.confirm_password') }}
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
