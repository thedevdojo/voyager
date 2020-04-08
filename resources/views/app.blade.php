<!doctype html>
<html lang="{{ Voyager::getLocale() }}" locales="{{ implode(',', Voyager::getLocales()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ Str::finish(route('voyager.dashboard'), '/') }}">

    <title>@yield('page-title') - Voyager</title>
    <link href="{{ Voyager::assetUrl('css/voyager.css') }}" rel="stylesheet">
    @foreach (VoyagerPlugins::getPluginsByType('theme')->where('enabled') as $theme)
    <link href="{{ $theme->getStyleRoute() }}" rel="stylesheet">
    @endforeach
</head>

<body>
    <div class="h-screen flex overflow-hidden" id="voyager">
        <fade-transition>
            <div class="loader" v-if="pageLoading">
                <helm class="icon rotating-cw"></helm>
            </div>
        </fade-transition>
        @include('voyager::sidebar')
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <main class="flex-1 relative z-0 overflow-y-auto pt-2 pb-6 outline-none">
                @include('voyager::navbar')
                <div class="mx-auto sm:px-3 md:px-4">
                    @yield('content')
                </div>
            </main>
        </div>
        <notifications></notifications>
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
            vm.pageLoading = false;
        });

        vm.messages.forEach(function (m) {
            if (m.type == 'info') {
                vm.$notify.info(m.message);
            } else if (m.type == 'success') {
                vm.$notify.success(m.message);
            } else if (m.type == 'warning') {
                vm.$notify.warning(m.message);
            } else if (m.type == 'error') {
                vm.$notify.error(m.message);
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
        this.$globals.debug = {{ var_export(config('app.debug') ?? false, true) }};

        var sidebar_open = this.$globals.getCookie('sidebar-open');
        if (sidebar_open == 'false') {
            this.sidebarOpen = false;
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