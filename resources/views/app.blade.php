<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="{{ Voyager::assetUrl('css/voyager.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100">

<div id="app" class="flex">

    @include('voyager::sidebar')

    <div class="app-wrapper">

        @include('voyager::navbar')

        <div id="voyager-loader">
            <v-icon id="voyager-loader-icon"  class="icon" spin name="dharmachakra"></v-icon>
        </div>

        <main class="py-4">
            @yield('content')
        </main>

    </div>

</div>

</body>
<script src="{{ Voyager::assetUrl('js/voyager.js') }}"></script>
</html>
