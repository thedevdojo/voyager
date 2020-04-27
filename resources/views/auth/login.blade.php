<!doctype html>
<html lang="{{ Voyager::getLocale() }}" locales="{{ implode(',', Voyager::getLocales()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ Str::finish(route('voyager.dashboard'), '/') }}">

    <title>{{ __('voyager::auth.login') }} - {{ VoyagerSettings::setting('admin.title', 'Voyager') }}</title>
    <link href="{{ Voyager::assetUrl('css/voyager.css') }}" rel="stylesheet">
    @forelse (VoyagerPlugins::getPluginsByType('theme')->where('enabled') as $theme)
        <link href="{{ $theme->getStyleRoute() }}" rel="stylesheet">
    @empty
        <link href="{{ Voyager::assetUrl('css/colors.css') }}" rel="stylesheet">
    @endforelse
</head>

<body>

    <div class="h-screen bg-gray-50 dark:bg-gray-800 flex flex-col justify-center py-12 sm:px-6 lg:px-8" id="voyager-login">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="justify-center flex text-center">
                <icon icon="helm" size="16" class="text-black"></icon>
            </div>
            <p class="mt-6 text-center text-sm leading-5 text-gray-600 max-w">
                Welcome to Voyager
            </p>
            <h2 class="mt-2 text-center text-3xl leading-9 font-extrabold text-gray-900">
                Sign in to your account
            </h2>
            
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">

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
            this.$store.toggleDarkMode();
        }
    },
});
</script>
@yield('js')
</html>