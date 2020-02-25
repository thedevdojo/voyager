<!doctype html>
<html lang="{{ Voyager::getLocale() }}" locales="{{ implode(',', Voyager::getLocales()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ Str::finish(route('voyager.dashboard'), '/') }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="{{ Voyager::assetUrl('css/voyager.css') }}" rel="stylesheet">
</head>
<body>

        <!--div id="voyager-loader">
            <img src="{{ Voyager::assetUrl('images/logo-icon.png') }}" alt="Voyager Loader">
        </div-->

<div id="voyager" class="flex m-auto">

    @if(!isset($sidebar) || (isset($sidebar) && $sidebar))
        @include('voyager::sidebar')
    @endif

        <transition name="fade">
            <div id="voyager-loader" v-if="page_loading">
                <!--v-icon id="voyager-loader-icon"  class="icon" spin name="dharmachakra"></v-icon-->
            </div>
        </transition>

        <main class="px-5 flex-1 ml-56">

            @if(!isset($sidebar) || (isset($sidebar) && $sidebar))
                @include('voyager::navbar')
            @endif

            @yield('content')

        </main>

    <vue-snotify></vue-snotify>
    <floating-button position="bottom-right" v-if="$language.localePicker">
        <locale-picker />
    </floating-button>
</div>

</body>
<script src="{{ Voyager::assetUrl('js/voyager.js') }}"></script>
@if (Voyager::getLocale() != 'en')
    <script src="https://npmcdn.com/flatpickr/dist/l10n/{{ Voyager::getLocale() }}.js"></script>
@endif
<script>
var voyager = new Vue({
    el: '#voyager',
    data: {
        page_loading: true,
        messages: {!! json_encode(Voyager::getMessages()) !!},
        formfields: {!! json_encode(Voyager::getFormfieldsDescription()) !!}
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
        this.$globals.formfields = this.formfields;
        this.$globals.debug = {{ var_export(config('app.debug') ?? false, true) }};
    }
});
</script>
@yield('js')
</html>
