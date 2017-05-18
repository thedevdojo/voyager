@extends('voyager::welcome')

@section('content')

    <h2>Reset Password:</h2>

    <form action="{{ route('voyager.password_email') }}" method="POST">
        {{ csrf_field() }}
        <div class="group{{ $errors->has('email') ? ' has-error' : '' }}">
          <input type="text" name="email" value="{{ old('email') }}" required>
          <span class="highlight"></span>
          <span class="bar"></span>
          <label><i class="glyphicon glyphicon-user"></i><span class="span-input"> E-mail</span></label>
        </div>

        <button type="submit" class="btn btn-block login-button">
            <span class="signingin hidden"><span class="glyphicon glyphicon-refresh"></span> Sending Password Reset Link...</span>
            <span class="signin">Send Password Reset Link</span>
        </button>
    </form>

    @if (session('status'))
        <div class="alert alert-success">
            <ul class="list-unstyled">
                <li>{{ session('status') }}</li>
            </ul>
        </div>
    @endif

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
