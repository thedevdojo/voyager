<!DOCTYPE html>
<html>
<head>
    {{-- <title>{{Voyager::setting('admin_title')}} - {{Voyager::setting('admin_description')}}</title> --}}
    <title>@yield('page_title',Voyager::setting('admin_title') . " - " . Voyager::setting('admin_description'))</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <!-- Fonts -->
    <link href='https://fonts.googleapis.com/css?family=Roboto+Condensed:300,400|Lato:300,400,700,900' rel='stylesheet'
          type='text/css'>

    <!-- CSS Libs -->
    <link rel="stylesheet" type="text/css" href="{{ voyager_asset('lib/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ voyager_asset('lib/css/animate.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ voyager_asset('lib/css/bootstrap-switch.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ voyager_asset('lib/css/checkbox3.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ voyager_asset('lib/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ voyager_asset('lib/css/dataTables.bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ voyager_asset('lib/css/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ voyager_asset('lib/css/toastr.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ voyager_asset('lib/css/perfect-scrollbar.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ voyager_asset('css/bootstrap-toggle.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ voyager_asset('js/icheck/icheck.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ voyager_asset('js/datetimepicker/bootstrap-datetimepicker.min.css') }}">
    <!-- CSS App -->
    <link rel="stylesheet" type="text/css" href="{{ voyager_asset('css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ voyager_asset('css/themes/flat-blue.css') }}">

    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:300,400,500,300italic">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ voyager_asset('images/logo-icon.png') }}" type="image/x-icon">

    <!-- CSS Fonts -->
    <link rel="stylesheet" href="{{ voyager_asset('fonts/voyager/styles.css') }}">
    <script type="text/javascript" src="{{ voyager_asset('lib/js/jquery.min.js') }}"></script>
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.0/themes/smoothness/jquery-ui.css">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.0/jquery-ui.min.js"></script>

    @yield('css')

    <!-- Voyager CSS -->
    <link rel="stylesheet" href="{{ voyager_asset('css/voyager.css') }}">

    <!-- Few Dynamic Styles -->
    <style type="text/css">
        .flat-blue .side-menu .navbar-header, .widget .btn-primary, .widget .btn-primary:focus, .widget .btn-primary:hover, .widget .btn-primary:active, .widget .btn-primary.active, .widget .btn-primary:active:focus{
            background:{{ config('voyager.primary_color','#22A7F0') }};
            border-color:{{ config('voyager.primary_color','#22A7F0') }};
        }
        .breadcrumb a{
            color:{{ config('voyager.primary_color','#22A7F0') }};
        }
    </style>

    @if(!empty(config('voyager.additional_css')))<!-- Additional CSS -->
    @foreach(config('voyager.additional_css') as $css)<link rel="stylesheet" type="text/css" href="{{ asset($css) }}">@endforeach
    @endif

    @yield('head')
</head>

<body class="flat-blue">

<div id="voyager-loader">
    <?php $admin_loader_img = Voyager::setting('admin_loader', ''); ?>
    @if($admin_loader_img == '')
        <img src="{{ voyager_asset('images/logo-icon.png') }}" alt="Voyager Loader">
    @else
        <img src="{{ Voyager::image($admin_loader_img) }}" alt="Voyager Loader">
    @endif
</div>

<?php
$user_avatar = Voyager::image(Auth::user()->avatar);
if ((substr(Auth::user()->avatar, 0, 7) == 'http://') || (substr(Auth::user()->avatar, 0, 8) == 'https://')) {
    $user_avatar = Auth::user()->avatar;
}
?>

<div class="app-container">
    <div class="fadetoblack visible-xs"></div>
    <div class="row content-container">
        @include('voyager::dashboard.navbar')
        @include('voyager::dashboard.sidebar')
        <!-- Main Content -->
        <div class="container-fluid">
            <div class="side-body padding-top">
                @yield('page_header')
                @yield('content')
            </div>
        </div>
    </div>
</div>
@include('voyager::partials.app-footer')
<script>
    (function(){
            var appContainer = document.querySelector('.app-container'),
                sidebar = appContainer.querySelector('.side-menu'),
                navbar = appContainer.querySelector('nav.navbar.navbar-top'),
                loader = document.getElementById('voyager-loader'),
                anchor = document.getElementById('sidebar-anchor'),
                hamburgerMenu = document.querySelector('.hamburger'),
                sidebarTransition = sidebar.style.transition,
                navbarTransition = navbar.style.transition,
                containerTransition = appContainer.style.transition;

            sidebar.style.WebkitTransition = sidebar.style.MozTransition = sidebar.style.transition =
            appContainer.style.WebkitTransition = appContainer.style.MozTransition = appContainer.style.transition = 
            navbar.style.WebkitTransition = navbar.style.MozTransition = navbar.style.transition = 'none';
            
            if (window.localStorage && window.localStorage['voyager.stickySidebar'] == 'true') {
                appContainer.className += ' expanded';
                loader.style.left = (sidebar.clientWidth/2)+'px';
                anchor.className += ' active';
                anchor.dataset.sticky = anchor.title;
                anchor.title = anchor.dataset.unstick;
                hamburgerMenu.className += ' is-active';
            }

            navbar.style.WebkitTransition = navbar.style.MozTransition = navbar.style.transition = navbarTransition;
            sidebar.style.WebkitTransition = sidebar.style.MozTransition = sidebar.style.transition = sidebarTransition;
            appContainer.style.WebkitTransition = appContainer.style.MozTransition = appContainer.style.transition = containerTransition;
    })();
</script>
<!-- Javascript Libs -->
<script type="text/javascript" src="{{ voyager_asset('lib/js/bootstrap.min.js') }}"></script>
<script type="text/javascript" src="{{ voyager_asset('lib/js/bootstrap-switch.min.js') }}"></script>
<script type="text/javascript" src="{{ voyager_asset('lib/js/jquery.matchHeight-min.js') }}"></script>
<script type="text/javascript" src="{{ voyager_asset('lib/js/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript" src="{{ voyager_asset('lib/js/dataTables.bootstrap.min.js') }}"></script>
<script type="text/javascript" src="{{ voyager_asset('lib/js/toastr.min.js') }}"></script>
<script type="text/javascript" src="{{ voyager_asset('lib/js/perfect-scrollbar.jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ voyager_asset('js/select2/select2.min.js') }}"></script>
<script type="text/javascript" src="{{ voyager_asset('js/bootstrap-toggle.min.js') }}"></script>
<script type="text/javascript" src="{{ voyager_asset('js/jquery.cookie.js') }}"></script>
<script type="text/javascript" src="{{ voyager_asset('js/moment-with-locales.min.js') }}"></script>
<script type="text/javascript" src="{{ voyager_asset('js/datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
<!-- Javascript -->
<script type="text/javascript" src="{{ voyager_asset('js/readmore.min.js') }}"></script>
<script type="text/javascript" src="{{ voyager_asset('js/val.js') }}"></script>
<script type="text/javascript" src="{{ voyager_asset('js/app.js') }}"></script>
<script type="text/javascript" src="{{ voyager_asset('js/helpers.js') }}"></script>
@if(!empty(config('voyager.additional_js')))<!-- Additional Javascript -->
@foreach(config('voyager.additional_js') as $js)<script type="text/javascript" src="{{ asset($js) }}"></script>@endforeach
@endif

<script>
    @if(Session::has('alerts'))
        let alerts = {!! json_encode(Session::get('alerts')) !!};

        displayAlerts(alerts, toastr);
    @endif

    @if(Session::has('message'))
    
    // TODO: change Controllers to use AlertsMessages trait... then remove this
    var alertType = {!! json_encode(Session::get('alert-type', 'info')) !!};
    var alertMessage = {!! json_encode(Session::get('message')) !!};
    var alerter = toastr[alertType];

    if (alerter) {
        alerter(alertMessage);
    } else {
        toastr.error("toastr alert-type " + alertType + " is unknown");
    }

    @endif
</script>
@yield('javascript')
</body>
</html>