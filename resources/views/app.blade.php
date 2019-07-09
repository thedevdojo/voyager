<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="{{ Voyager::assetUrl('css/voyager.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-200">

<div id="voyager" class="flex container m-auto">

    @include('voyager::sidebar')

    <div class="flex-initial w-full">

        @include('voyager::navbar')

        <transition name="fade">
            <div id="voyager-loader" v-if="page_loading">
                <v-icon id="voyager-loader-icon"  class="icon" spin name="dharmachakra"></v-icon>
            </div>
        </transition>

        <main class="py-6">
            @yield('content')
        </main>

    </div>

</div>

</body>
<script src="{{ Voyager::assetUrl('js/voyager.js') }}"></script>
</html>
