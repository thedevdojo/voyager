<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="none" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="admin login">
    <title>Admin - {{ Voyager::setting("title") }}</title>
    <link rel="stylesheet" href="{{ voyager_asset('lib/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ voyager_asset('css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ voyager_asset('css/login.css') }}">
    <style>
        body {
            background-image:url('{{ Voyager::image( Voyager::setting("admin_bg_image"), config('voyager.assets_path') . "/images/bg.jpg" ) }}');
            background-color: {{ Voyager::setting("admin_bg_color", "#FFFFFF" ) }};
        }
        .login-sidebar{
            border-top:5px solid {{ config('voyager.primary_color','#22A7F0') }};
        }
        .login-sidebar:after {
            background: linear-gradient(-135deg, {{config('voyager.login.gradient_a','#ffffff')}}, {{config('voyager.login.gradient_b','#ffffff')}});
            background: -webkit-linear-gradient(-135deg, {{config('voyager.login.gradient_a','#ffffff')}}, {{config('voyager.login.gradient_b','#ffffff')}});
        }
        .login-button, .bar:before, .bar:after{
            background:{{ config('voyager.primary_color','#22A7F0') }};
        }

    </style>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="faded-bg animated"></div>
        <div class="hidden-xs col-sm-7 col-md-8">
            <div class="clearfix">
                <div class="col-sm-12 col-md-10 col-md-offset-2">
                    <div class="logo-title-container">
                        <?php $admin_logo_img = Voyager::setting('admin_icon_image', ''); ?>
                        @if($admin_logo_img == '')
                        <img class="img-responsive pull-left logo hidden-xs animated fadeIn" src="{{ voyager_asset('images/logo-icon-light.png') }}" alt="Logo Icon">
                        @else
                        <img class="img-responsive pull-left logo hidden-xs animated fadeIn" src="{{ Voyager::image($admin_logo_img) }}" alt="Logo Icon">
                        @endif
                        <div class="copy animated fadeIn">
                            <h1>{{ Voyager::setting('admin_title', 'Voyager') }}</h1>
                            <p>{{ Voyager::setting('admin_description', 'Welcome to Voyager. The Missing Admin for Laravel') }}</p>
                        </div>
                    </div> <!-- .logo-title-container -->
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-5 col-md-4 login-sidebar">

            <div class="login-container">

                <p>Sign in to your account</p>

                <form action="{{ route('voyager.login') }}" method="POST">
                    {{ csrf_field() }}
                    <div class="form-group form-group-default ">
                        <label>Email</label>
                        <div class="controls">
                            <input type="text" name="email" id="email" value="{{ old('email') }}" placeholder="Enter your email" class="form-control" required>
                         </div>
                    </div>

                    <div class="form-group form-group-default ">
                        <label>Password</label>
                        <div class="controls">
                            <input type="password" name="password" placeholder="Enter your password" class="form-control" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-block login-button">
                        <span class="signingin hidden"><span class="glyphicon glyphicon-refresh"></span> Loggin in...</span>
                        <span class="signin">Login</span>
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

            </div> <!-- .login-container -->
            
        </div> <!-- .login-sidebar -->
    </div> <!-- .row -->
</div> <!-- .container-fluid -->
<script>
    var btn = document.querySelector('button[type="submit"]');
    var form = document.forms[0];
    btn.addEventListener('click', function(ev){
        if (form.checkValidity()) {
            btn.querySelector('.signingin').className = 'signingin';
            btn.querySelector('.signin').className = 'signin hidden';
        } else {
            ev.preventDefault();
        }
    });
    document.getElementById('email').focus();
</script>
</body>
</html>
