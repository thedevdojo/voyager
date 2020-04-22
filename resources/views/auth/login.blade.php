<!doctype html>
<html lang="{{ Voyager::getLocale() }}" locales="{{ implode(',', Voyager::getLocales()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ Str::finish(route('voyager.dashboard'), '/') }}">

    <title>@yield('page-title') - Voyager</title>
    <link href="{{ Voyager::assetUrl('css/voyager.css') }}" rel="stylesheet">
    @forelse (VoyagerPlugins::getPluginsByType('theme')->where('enabled') as $theme)
        <link href="{{ $theme->getStyleRoute() }}" rel="stylesheet">
    @empty
        <link href="{{ Voyager::assetUrl('css/colors.css') }}" rel="stylesheet">
    @endforelse
</head>

<body>
<div class="flex h-screen" id="voyager-login">
    <div class="w-0 md:w-3/5 bg-gray-200 bg-cover" style="background-image:url('{{ Voyager::assetUrl('images/login-bg.png') }}');">
        <div class="flex justify-left pl-4 items-center h-16 logo">
            <icon icon="helm" size="8" class="text-black"></icon>
            <span class="font-bold ml-2 text-gray-900 mt-1 text-xl uppercase">Voyager</span>
        </div>
    </div>
    <div class="w-full md:w-2/5 bg-white-100 dark:bg-gray-900 flex h-screen items-center">
        <div class="w-0 xl:w-1/5"></div>
        <div class="w-full xl:w-3/5 mx-6 xl:mx-0">
            <login error="{{ Session::get('error', null) }}" success="{{ Session::get('success', null) }}" :old="{{ json_encode(old()) }}">
                @if ($authentication->loginView())
                <div slot="login">
                    {!! $authentication->loginView() !!}
                </div>
                @endif

                @if ($authentication->forgotPasswordView())
                <div slot="forgot_password">
                    {!! $authentication->forgotPasswordView() !!}
                </div>
                @endif
            </login>
        </div>
        <div class="w-0 xl:w-1/5"></div>
    </div>
</div>
</body>
<script src="{{ Voyager::assetUrl('js/voyager.js') }}"></script>
<script>
var voyager = new Vue({
    el: '#voyager-login',
    created: function () {
        this.$language.localization = {!! Voyager::getLocalization() !!};
        this.$store.routes = {!! Voyager::getRoutes() !!};
        this.$store.debug = {{ var_export(config('app.debug') ?? false, true) }};

        var dark_mode = this.getCookie('dark-mode');
        if (dark_mode == 'true') {
            this.$store.darkmode = true;
        }
    },
});
</script>
@yield('js')
</html>