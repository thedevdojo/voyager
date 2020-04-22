<!doctype html>
<html lang="{{ Voyager::getLocale() }}" locales="{{ implode(',', Voyager::getLocales()) }}" dir="{{ __('voyager::generic.is_rtl') == 'true' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ Str::finish(route('voyager.dashboard'), '/') }}">

    <title>@yield('page-title') - Voyager</title>
    <link href="{{ Voyager::assetUrl('css/voyager.css') }}" rel="stylesheet">
    <link href="{{ Voyager::assetUrl('css/colors.css') }}" rel="stylesheet">
    @foreach (VoyagerPlugins::getPluginsByType('theme')->where('enabled') as $theme)
        <link href="{{ $theme->getStyleRoute() }}" rel="stylesheet">
    @endforeach
</head>

<body>
    <slide-x-left-transition class="h-screen flex overflow-hidden" id="voyager" tag="div" group>
        <div key="loader">
            <fade-transition :duration="500">
                <div class="loader" v-if="pageLoading">
                    <icon icon="helm" size="auto" class="block icon rotating-cw"></icon>
                </div>
            </fade-transition>
        </div>
        @include('voyager::sidebar')
        <div class="flex flex-col w-0 flex-1 overflow-hidden" :key="'content'">
            <main class="flex-1 relative z-0 overflow-y-auto pt-2 pb-6 outline-none">
                <span id="scroll-top"></span>
                @include('voyager::navbar')
                <div class="mx-auto sm:px-3 md:px-4">
                    @yield('content')
                </div>
            </main>
        </div>
        <notifications key="notifications"></notifications>
    </slide-x-left-transition>
</body>
<script src="{{ Voyager::assetUrl('js/voyager.js') }}"></script>
<script>
var voyager = new Vue({
    el: '#voyager',
    data: this.$store,
    mounted: function () {
        var vm = this;

        document.addEventListener("DOMContentLoaded", function(event) {
            vm.$store.pageLoading = false;
        });

        var messages = {!! Voyager::getMessages()->toJson() !!};

        messages.forEach(function (m) {
            vm.$notify.notify(m.message, null, m.color, m.timeout);
        });

        document.addEventListener('keydown', function (e) {
            if (event.ctrlKey) {
                if (e.keyCode == 38 || e.keyCode == 39) {
                    vm.$language.nextLocale();
                } else if (e.keyCode == 37 || e.keyCode == 40) {
                    vm.$language.previousLocale();
                }
            }
        });
    },
    created: function () {
        var vm = this;

        this.$language.localization = {!! Voyager::getLocalization() !!};
        this.$store.routes = {!! Voyager::getRoutes() !!};
        this.$store.debug = {{ var_export(config('app.debug') ?? false, true) }};

        var dark_mode = this.getCookie('dark-mode');
        if (dark_mode == 'true') {
            vm.$store.toggleDarkMode();
        }

        var sidebar_open = this.getCookie('sidebar-open');
        if (sidebar_open == 'false') {
            this.$store.toggleSidebar();
        }
    },
    watch: {
        sidebarOpen: function (open) {
            this.setCookie('sidebar-open', (open ? 'true' : 'false'), 360);
        },
        '$store.darkmode': function (darkmode) {
            this.setCookie('dark-mode', (darkmode ? 'true' : 'false'), 360);
        },
        '$store.sidebarOpen': function (open) {
            this.setCookie('sidebar-open', (open ? 'true' : 'false'), 360);
        }
    }
});
</script>
@yield('js')
</html>