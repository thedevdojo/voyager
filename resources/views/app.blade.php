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
<div id="voyager" class="flex m-auto">

    @if(!isset($sidebar) || (isset($sidebar) && $sidebar))
        @include('voyager::sidebar')
    @endif
    <transition name="fade">
        <div class="loader" v-if="page_loading">
            <helm class="icon rotating"></helm>
        </div>
    </transition>

    <main class="px-5 flex-1 ml-56">

        @if(!isset($sidebar) || (isset($sidebar) && $sidebar))
            @include('voyager::navbar')
        @endif

        @yield('content')

    </main>
    <vue-snotify></vue-snotify>
</div>

</body>
<script src="{{ Voyager::assetUrl('js/voyager.js') }}"></script>
<script>
var voyager = new Vue({
    el: '#voyager',
    data: {
        page_loading: true,
        messages: {!! Voyager::getMessages()->toJson() !!},
    },
    mounted: function () {
        var vm = this;

        document.addEventListener("DOMContentLoaded", function(event) {
            // Hide voyager-loader
            vm.page_loading = false;
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
    }
});
</script>
@yield('js')
</html>
