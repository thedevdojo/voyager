@extends('voyager::auth.master')

@section('content')
    <div class="login-container">
        <p>{{ __('voyager::login.signin_below') }}</p>

        <form action="{{ route('voyager.login') }}" method="POST">
            {{ csrf_field() }}
            
            <div class="form-floating mb-3" id="emailGroup">
                <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="{{ __('voyager::generic.email') }}" class="form-control" required>
                <label for="email">{{ __('voyager::generic.email') }}</label>
            </div>

            <div class="form-floating mb-3" id="passwordGroup">
                <input type="password" name="password" id="password" placeholder="{{ __('voyager::generic.password') }}" class="form-control" required>
                <label for="password">{{ __('voyager::generic.password') }}</label>
            </div>

            <div class="mb-3" id="rememberMeGroup">                   
                <div class="form-check">
                    <input type="checkbox" name="remember" id="remember" value="1" class="form-check-input">
                    <label class="form-check-label" for="remember">
                        {{ __('voyager::generic.remember_me') }}
                    </label>
                </div>
            </div>

            <div>
                <button type="submit" class="btn btn-primary w-md login-button">
                    <span class="signingin hidden"><span class="voyager-refresh"></span> {{ __('voyager::login.loggingin') }}...</span>
                    <span class="signin">{{ __('voyager::generic.login') }}</span>
                </button>
            </div>
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

    </div> <!-- .login-container -->
@endsection

@section('post_js')
    <script>
        var btn = document.querySelector('button[type="submit"]');
        var form = document.forms[0];
        var email = document.querySelector('[name="email"]');
        var password = document.querySelector('[name="password"]');
        btn.addEventListener('click', function(ev){
            if (form.checkValidity()) {
                btn.querySelector('.signingin').className = 'signingin';
                btn.querySelector('.signin').className = 'signin hidden';
            } else {
                ev.preventDefault();
            }
        });
        email.focus();
        document.getElementById('emailGroup').classList.add("focused");

        // Focus events for email and password fields
        email.addEventListener('focusin', function(e){
            document.getElementById('emailGroup').classList.add("focused");
        });
        email.addEventListener('focusout', function(e){
            document.getElementById('emailGroup').classList.remove("focused");
        });

        password.addEventListener('focusin', function(e){
            document.getElementById('passwordGroup').classList.add("focused");
        });
        password.addEventListener('focusout', function(e){
            document.getElementById('passwordGroup').classList.remove("focused");
        });
    </script>
@endsection
