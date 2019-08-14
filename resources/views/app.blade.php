<!doctype html>
<html lang="{{ app()->getLocale() }}" locales="en,de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="{{ Voyager::assetUrl('css/voyager.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-200">

        <!--div id="voyager-loader">
            <img src="{{ Voyager::assetUrl('images/logo-icon.png') }}" alt="Voyager Loader">
        </div-->

<div id="voyager" class="flex m-auto">

    @if(!isset($sidebar) || (isset($sidebar) && $sidebar))
        @include('voyager::sidebar')
    @endif

    @if(isset($sidebarSecondary))
        @include('voyager::partials.sidebar-secondary')
    @endif

    <div class="flex-initial w-full @if(isset($sidebarSecondary)){{ 'ml-72' }}@else{{ 'ml-16' }}@endif">

        @if(!isset($sidebar) || (isset($sidebar) && $sidebar))
            @include('voyager::navbar')
        @endif

        <transition name="fade">
            <div id="voyager-loader" v-if="page_loading">
                <!--v-icon id="voyager-loader-icon"  class="icon" spin name="dharmachakra"></v-icon-->
            </div>
        </transition>

        <main class="mx-4">
            @yield('content')
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
        page_loading: true,
        messages: {!! json_encode(Voyager::getMessages()) !!},
        localization: {!! Voyager::getLocalization() !!},
        routes: {!! Voyager::getRoutes() !!},
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
                vm.$snotify.simple(m.message);
            }
        });
    },
    created: function () {
        this.$eventHub.localization = this.localization;
        this.$eventHub.routes = this.routes;
        this.$eventHub.formfields = this.formfields;
    }
});
</script>
@yield('js')
</html>
