<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="admin login">
    <meta name="author" content="">
    <title>Admin Login</title>
    {{-- Voyager CSS --}}
    <link rel="stylesheet" href="{{ config('voyager.assets_path') }}/css/voyager.css">
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:300,400,500,300italic">
    <link href="https://file.myfontastic.com/QLbQY2QVvDNQgGeBRf7fWh/icons.css" rel="stylesheet">

    <style>
        .login-page {
            background-image: url('{{ Voyager::image( Voyager::setting("admin_bg_image"), config('voyager.assets_path') . "/images/bg.jpg" ) }}');
            background-size: cover;
            margin: 0;
            padding: 0;
        }

        .logo-img {
            width: 100px;
            z-index: 999;
            position: relative;
            float: left;
            -webkit-animation: spin 1s linear 1;
            -moz-animation: spin 1s linear 1;
            animation: spin 1s linear 1;
        }

        #bgdim {
            background: rgba(38, 50, 56, .6);
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        #login_section {
            width: 380px;
            height: 100%;
            position: absolute;
            right: 0;
            top: 0;
            text-align: center;
            background: #fff;
            z-index: 99;
        }

        #title_section {
            width: auto;
            position: absolute;
            margin-left: 120px;
            top: 50%;
            margin-top: -50px;
        }

        #title_section .copy {
            float: left;
        }

        #title_section h1 {
            display: inline-block;
            vertical-align: middle;
            color: #fff;
            z-index: 9999;
            position: relative;
            text-transform: uppercase;
            font-size: 50px;
            font-weight: 400;
            top: -10px;
            line-height: 45px;
            margin: 20px 0 0 20px;
        }

        #title_section p {
            color: #fff;
            font-size: 20px;
            max-width: 650px;
            opacity: .6;
            position: relative;
            z-index: 99;
            font-weight: 200;
            margin-top: 0;
            left: 25px;
        }

        #login_section h2 {
            text-align: left;
            margin-left: 50px;
            font-weight: 200;
            margin-bottom: 0;
            margin-top: 3px;
            color: #444;
        }

        #login_section .btn {
            background: #62A8EA;
            border-radius: 0;
            color: #fff;
            width: 380px;
            margin-left: 0;
            display: block;
            text-align: left;
            padding: 15px 20px 15px 50px;
            border-width: 0;
        }

        .btn-login {
            text-decoration: none;
        }

        .btn-login i {
            border-right: 0;
            position: relative;
            top: 2px;
        }

        #login_section p {
            font-weight: 100;
            margin-top: 10px;
            float: left;
            margin-left: 50px;
        }

        #login_section .content {
            position: absolute;
            top: 50%;
            margin-top: -132px;
        }

        #login input {
            padding: 20px 50px;
            border: 0;
            background: #f5f5f5;
            border-radius: 0;
            float: left;
            margin-left: 0;
            margin-bottom: 10px;
            width: 278px;
            font-size: 12px;
            font-weight: 200;
        }

        textarea, input, button {
            outline: none;
        }

        button {
            cursor: pointer;
        }

        .btn-loading {
            width: 16px;
            height: 16px;
            float: left;
            margin: 3px 3px 0 -1px;
            -webkit-animation: spin 0.4s linear infinite;
            -moz-animation: spin 0.4s linear infinite;
            animation: spin 0.4s linear infinite;
        }

        .login_loader {
            display: none;
        }

        .error-login {
            padding: 1em;
            text-align: center;
            color: #DC143C;
        }

        @-moz-keyframes spin {
            100% {
                -moz-transform: rotate(90deg);
            }
        }

        @-webkit-keyframes spin {
            100% {
                -webkit-transform: rotate(90deg);
            }
        }

        @keyframes spin {
            100% {
                -webkit-transform: rotate(90deg);
                transform: rotate(90deg);
            }
        }
    </style>
</head>
<body class="login-page">
    <div id="bgdim"></div>

    <div id="title_section">
        <img class="logo-img" src="{{ config('voyager.assets_path') }}/images/logo-icon-light.png" alt="Admin Login">
        <div class="copy">
            <h1>{{ Voyager::setting('admin_title', 'Voyager') }}</h1>
            <p>{{ Voyager::setting('admin_description', 'Welcome to Voyager. The Missing Admin for Laravel') }}</p>
        </div>
        <div style="clear:both"></div>
    </div>

    <div id="login_section">
        <div class="content">
            <h2>Sign In</h2>
            <p>Sign in below:</p>
            <div style="clear:both"></div>
            <form action="{{ route('voyager.login') }}" method="POST" id="login">
                {{ csrf_field() }}
                <input type="text" class="form-control" name="email" placeholder="email address" value="{{ old('email') }}">
                <input type="password" class="form-control" name="password" placeholder="password">
                <button class="btn btn-primary btn-login" id="voyager-login-btn">
                    <span class="login_text"><i class="voyager-lock"></i> Login</span>
                    <span class="login_loader">
                        <img class="btn-loading" src="{{ config('voyager.assets_path') }}/images/logo-icon-light.png"> Logging in
                    </span>
                </button>
            </form>
            @if (count($errors))
                <div class="error-login">
                    The given credentials don't match with an user registered.
                </div>
            @endif
        </div>
    </div>

    <script>
        login_btn = document.getElementById("voyager-login-btn");
        login_btn.addEventListener("click", function () {
            var originalHeight = login_btn.offsetHeight;
            login_btn.style.height = originalHeight + 'px';
            document.querySelector('#voyager-login-btn span.login_text').style.display = 'none';
            document.querySelector('#voyager-login-btn span.login_loader').style.display = 'block';
        });
    </script>

</body>
</html>
