<!doctype html>
<html lang="{{ Voyager::getLocale() }}" locales="{{ implode(',', Voyager::getLocales()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ Str::finish(route('voyager.dashboard'), '/') }}">

    <title>@yield('page-title') - {{ Voyager::setting('admin.title', true, 'Voyager') }}</title>
    <link href="{{ Voyager::assetUrl('css/voyager.css') }}" rel="stylesheet">
    @foreach (Voyager::getPluginsByType('theme')->where('enabled') as $theme)
    <link href="{{ $theme->getStyleRoute() }}" rel="stylesheet">
    @endforeach
</head>

<body>
    <div class="h-screen flex overflow-hidden" id="voyager">
        <transition name="fade">
            <div class="loader" v-if="pageLoading">
                <helm class="icon rotating"></helm>
            </div>
        </transition>
        @include('voyager::sidebar')
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <main class="flex-1 relative z-0 overflow-y-auto pt-2 pb-6 outline-none">
                @include('voyager::navbar')
                <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                    @yield('content')
                </div>
            </main>
        </div>
        <vue-snotify></vue-snotify>
    </div>

</body>
<script src="{{ Voyager::assetUrl('js/voyager.js') }}"></script>
<script>
var voyager = new Vue({
    el: '#voyager',
    data: {
        pageLoading: true,
        messages: {!! Voyager::getMessages()->toJson() !!},
        sidebarOpen: true,
        mobileSidebarOpen: false,
    },
    mounted: function () {
        var vm = this;

        document.addEventListener("DOMContentLoaded", function(event) {
            // Hide voyager-loader
            vm.pageLoading = false;
        });

        vm.messages.forEach(function (m) {
            if (m.type == 'info') {
                vm.$snotify.info(m.message);
            } else if (m.type == 'success') {
                vm.$snotify.success(m.message);
            } else if (m.type == 'warning') {
                vm.$snotify.warning(m.message);
            } else if (m.type == 'error') {
                vm.$snotify.error(m.message);
            } else if (m.type == 'debug') {
                if (vm.debug) {
                    vm.debug(m.message);
                }
            }
        });
    },
    created: function () {
        this.$language.localization = {!! Voyager::getLocalization() !!};
        this.$globals.routes = {!! Voyager::getRoutes() !!};
        this.$globals.breads = {!! Bread::getBreads() !!};
        this.$globals.formfields = {!! Voyager::getFormfieldsDescription()->toJson() !!};
        this.$globals.debug = {{ var_export(config('app.debug') ?? false, true) }};
        this.$globals.searchPlaceholder = '{{ Bread::getBreadSearchPlaceholder() }}';

        var sidebar_open = this.$globals.getCookie('sidebar-open');
        if (sidebar_open == 'false') {
            this.sidebarOpen = false;
        }
    },
    methods: {
        clicky: function (f) {
            console.log(f);
        }
    },
    watch: {
        sidebarOpen: function (open) {
            this.$globals.setCookie('sidebar-open', (open ? 'true' : 'false'), 360);
        }
    }
});
</script>
@yield('js')

</html>