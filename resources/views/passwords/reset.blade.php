@extends('voyager::welcome')

@section('content')

    <h2>Reset Password:</h2>

    <form action="{{ route('voyager.password_reset') }}" method="POST">
        {{ csrf_field() }}
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="group{{ $errors->has('email') ? ' has-error' : '' }}">
            <input type="text" name="email" value="{{ $email or old('email') }}" required autofocus>
            <span class="highlight"></span>
            <span class="bar"></span>
            <label><i class="glyphicon glyphicon-user"></i><span class="span-input"> E-mail</span></label>
        </div>

        <div class="group{{ $errors->has('password') ? ' has-error' : '' }}">
            <input type="password" name="password" required>
            <span class="highlight"></span>
            <span class="bar"></span>
            <label><i class="glyphicon glyphicon-lock"></i><span class="span-input"> Password</span></label>
        </div>

        <div class="group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
            <input type="password" name="password_confirmation" required>
            <span class="highlight"></span>
            <span class="bar"></span>
            <label><i class="glyphicon glyphicon-lock"></i><span class="span-input"> Confirm Password</span></label>
        </div>

        <button type="submit" class="btn btn-block login-button">
            <span class="signingin hidden"><span class="glyphicon glyphicon-refresh"></span> Resetting Password...</span>
            <span class="signin">Reset Password</span>
        </button>
    </form>

    @if(!$errors->isEmpty())
        <div class="alert alert-black">
            <ul class="list-unstyled">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <a class="btn btn-link" href="{{ route('voyager.login') }}">Login again</a>

@endsection
