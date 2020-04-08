@extends('voyager::app')
@section('page-title', __('voyager::generic.dashboard'))
@section('content')
    <div class="flex">
        @forelse (Voyager::getWidgets() as $widget)
        <div class="{{ $widget->width }} w-3/6 m-1 rounded-lg px-6 py-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 shadow-sm">
            {!! $widget->view->render() !!}
        </div>
        @empty
        <div class="w-full rounded-lg px-6 py-4 flex flex-col bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 shadow-sm">
            <div class="flex items-center">
                <span class="w-6 h-6 mr-2 fill-current text-black dark:text-white">
                    <helm />
                </span>
                <h2 class="text-black dark:text-white font-bold text-lg leading-tight">Welcome to the New Voyager</h2>
            </div>
            <div class="text-gray-700 dark:text-gray-300 text-xs mt-3 font-medium">Voyager 2 has been re-built using Laravel and VueJS. There
                are a lot of other cool things about version 2
            </div>
            <div class="rounded-full px-4 py-1 self-end text-xs text-white dark:text-black bg-black dark:bg-white mt-5">Learn More</div>
        </div>
        @endforelse
    </div>
@endsection