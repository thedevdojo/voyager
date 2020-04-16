<!doctype html>
<html lang="{{ Voyager::getLocale() }}" locales="{{ implode(',', Voyager::getLocales()) }}" dir="ltr">

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
    <slide-x-left-transition class="h-screen flex overflow-hidden" id="voyager" tag="div" group>
        <div key="loader">
            <fade-transition :duration="500">
                <div class="loader" v-if="store.state.pageLoading">
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
    mounted: function () {
        var vm = this;

        document.addEventListener("DOMContentLoaded", function(event) {
            vm.store.commit('pageLoading', false);
        });

        var messages = {!! Voyager::getMessages()->toJson() !!};

        messages.forEach(function (m) {
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
        this.store.commit('routes', {!! Voyager::getRoutes() !!});
        this.store.commit('debug', {{ var_export(config('app.debug') ?? false, true) }});

        var dark_mode = this.getCookie('dark-mode');
        if (dark_mode == 'true') {
            this.store.commit('toggleDarkMode');
        }

        var sidebar_open = this.getCookie('sidebar-open');
        if (sidebar_open == 'false') {
            this.store.commit('setSidebar', false);
        }
    },
    watch: {
        sidebarOpen: function (open) {
            this.setCookie('sidebar-open', (open ? 'true' : 'false'), 360);
        },
        'store.state.darkmode': function (darkmode) {
            this.setCookie('dark-mode', (darkmode ? 'true' : 'false'), 360);
        },
        'store.state.sidebarOpen': function (open) {
            this.setCookie('sidebar-open', (open ? 'true' : 'false'), 360);
        }
    }
});
</script>
@yield('js')
</html>