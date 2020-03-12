@extends('voyager::app')
@section('page-title', __('voyager::generic.dashboard'))
@section('content')
    <!-- <h1 class="text-gray-800 uppercase font-bold tracking-widest text-xs mb-4">{{ __('voyager::generic.dashboard') }}</h1>
    <h2 class="text-blue-800 font-bold tracking-widest text-xl mb-4">Welcome to Voyager 2.0</h2>-->
    <div class="rounded-lg px-6 py-4 w-1/3 flex flex-col bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 shadow-sm">
        <div class="flex items-center">
            <span class="w-6 h-6 mr-2 fill-current text-black dark:text-white">
                <i class="uil uil-sun"></i>
            </span>
            <h2 class="text-black dark:text-white font-bold text-lg leading-tight">Welcome to the New Voyager</h2>
        </div>
        <div class="text-gray-700 dark:text-gray-300 text-xs mt-3 font-medium">Voyager 2 has been re-built using Laravel and VueJS. There
            are a lot of other cool things about version 2
        </div>
        <div class="rounded-full px-4 py-1 self-end text-xs text-white dark:text-black bg-black dark:bg-white mt-5">Learn More</div>
    </div>
@endsection