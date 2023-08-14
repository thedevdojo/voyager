<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" dir="{{ __('voyager::generic.is_rtl') == 'true' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="none" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="admin login">
    <title>@yield('title', 'Admin')</title>
    <link rel="stylesheet" href="{{ voyager_asset('css/app.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    @if (__('voyager::generic.is_rtl') == 'true')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-rtl/3.4.0/css/bootstrap-rtl.css">
        <link rel="stylesheet" href="{{ voyager_asset('css/rtl.css') }}">
    @endif
    <style>
        body {
            /* background-image: url('{{ Voyager::image(Voyager::setting('admin.bg_image'), voyager_asset('images/bg.jpg')) }}'); */
            /* background-color: {{ Voyager::setting('admin.bg_color', '#FFFFFF') }}; */
            background-color: #e1e2e2;
            width: 100vw;
            height: 100vh;
        }

        /* body.login .login-sidebar {
            border-top: 5px solid {{ config('voyager.primary_color', 'blue') }};
        } */

        @media (max-width: 767px) {
            body.login .login-sidebar {
                border-top: 0px !important;
                border-left: 5px solid {{ config('voyager.primary_color', '#22A7F0') }};
            }
        }

        body.login .form-group-default.focused {
            border-color: {{ config('voyager.primary_color', '#22A7F0') }};
        }

        /* .login-button,
        .bar:before,
        .bar:after {
            background: {{ config('voyager.primary_color', '#22A7F0') }};
        } */

        .remember-me-text {
            padding: 0 5px;
        }

        .hidden{
            display:none
        }
        .voyager-refresh {
            animation: spin 0.6s linear infinite;
        }
        form {
            max-width: 500px; /* adjust the value as needed */
            background: #fff;
            margin: 0 auto;
            box-shadow: 0px 0px 27px 0px rgba(0,0,0,0.75);
            -webkit-box-shadow: 0px 0px 27px 0px rgba(0,0,0,0.75);
            -moz-box-shadow: 0px 0px 27px 0px rgba(0,0,0,0.75);
        }
        
    </style>

    @yield('pre_css')
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
</head>

<body class=" d-flex justify-content-center align-items-center">

    <div class="container">
        @yield('content')
    </div> <!-- .container-fluid -->     


    @yield('post_js')
</body>

</html>