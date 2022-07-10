@extends('voyager::auth.master')

@section('content')
    <div class="login-container">

        @include('voyager::alerts')

        <form method="POST" action="{{ route('voyager.password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group form-group-default @error('email') has-error @enderror">
                <label for="email">{{ __('voyager::generic.email') }}</label>
                <input id="email" type="email" class="form-control" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" readonly>
            </div>

            <h6>{{ __('voyager::passwords.password_reset_instruction') }}</h6>

            <div class="form-group form-group-default @error('password') has-error @enderror">
                <label for="password">{{ __('voyager::generic.password') }}</label>
                <input id="password" type="password" class="form-control" name="password" required min="8" autocomplete="current-password">
            </div>

            <div class="form-group form-group-default">
                <label for="password">{{ __('voyager::passwords.confirm_password') }}</label>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required min="8" autocomplete="new-password">
            </div>

            <button type="submit" class="btn btn-block login-button">
                {{ __('voyager::passwords.change_password') }}
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
