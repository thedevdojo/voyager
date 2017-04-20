<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="none" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="admin login">
    <title>Admin - {{ Voyager::setting("title") }}</title>
    <!-- CSS Libs -->
    <link rel="stylesheet" type="text/css" href="{{ voyager_asset('lib/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ voyager_asset('lib/css/animate.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ voyager_asset('lib/css/toastr.min.css') }}">
	<link rel="stylesheet" href="{{ voyager_asset('fonts/voyager/styles.css') }}">
    <link rel="stylesheet" href="{{ voyager_asset('css/lockscreen.css') }}">
    <style>
        body {
            background-image:url('{{ Voyager::image( Voyager::setting("admin_bg_image"), config('voyager.assets_path') . "/images/bg.jpg" ) }}');
        }
        .unlock-button, .bar:before, .bar:after{
            background:{{ config('voyager.primary_color','#22A7F0') }};
        }

    </style>
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,700' rel='stylesheet' type='text/css'>
</head>
<body>
<!-- Designed with ♥ by davodavodavo3 -->
<div class="container-fluid">
    <div class="row">
		<div class="faded-bg animated"></div>

		<div class="col-xs-12 col-sm-6 col-md-4 col-sm-offset-3 col-md-offset-4 unlock-container animated slideInDown">

			<div class="jumbotron">

				<img src="{{ Voyager::image( Auth::user()->avatar ) }}" class="avatar"
					style="border-radius:50%; width:150px; height:150px; border:5px solid #fff;"
					alt="{{ Auth::user()->name }} avatar">
				<h4>{{ ucwords(Auth::user()->name) }}</h4>
				<h5>Enter your password to retrieve your session</h5>

				<div class="unlock-content">

					<form action="{{ route('voyager.unlock') }}" method="POST">
						{{ csrf_field() }}

						<div class="group">

							<input type="password" name="password" value="{{ old('password') }}" required>
							<span class="highlight"></span>
							<span class="bar"></span>
							<label>
								<i class="glyphicon glyphicon-lock"></i>
								<span class="span-input">Password</span>
							</label>

						</div>

						<button type="submit" class="btn btn-block unlock-button">

							<span class="unlock-on hidden">
								<span class="glyphicon glyphicon-refresh"></span> Unlock...
							</span>

							<span class="unlock">Unlock</span>

						</button>

						<a href="{{route('voyager.logout')}}">Or sign in as a different user</a>

				  </form>

				</div>

			</div>

		</div> <!-- .unlock-container -->
    </div> <!-- .row -->
</div> <!-- .container-fluid -->

<!-- Javascript Libs -->
<script type="text/javascript" src="{{ voyager_asset('lib/js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ voyager_asset('lib/js/bootstrap.min.js') }}"></script>
<script type="text/javascript" src="{{ voyager_asset('lib/js/toastr.min.js') }}"></script>
<script type="text/javascript" src="{{ voyager_asset('js/helpers.js') }}"></script>

<script>

	@if(Session::has('alerts'))
        let alerts = {!! json_encode(Session::get('alerts')) !!};

        displayAlerts(alerts, toastr);
    @endif

    var btn = document.querySelector('button[type="submit"]');
    var form = document.forms[0];
    btn.addEventListener('click', function(ev){
        if (form.checkValidity()) {
            btn.querySelector('.unlock-on').className = 'unlock-on';
            btn.querySelector('.unlock').className = 'unlock hidden';
        } else {
            ev.preventDefault();
        }
    });
</script>
</body>
</html>
