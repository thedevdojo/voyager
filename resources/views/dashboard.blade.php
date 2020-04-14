@extends('voyager::app')
@section('page-title', __('voyager::generic.dashboard'))
@section('content')
    <div class="flex">
        @forelse (Voyager::getWidgets() as $widget)
        <div class="{{ $widget->width }} w-3/6 m-1 rounded-lg px-6 py-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 shadow-sm">
            {!! $widget->view->render() !!}
        </div>
        @empty
        <card title="Welcome to Voyager II" icon="helm" :icon-size="8" class="w-full">
            <div>Voyager 2 has been re-built using Laravel and VueJS. There are a lot of other cool things about version 2</div>
        </card>
        @endforelse
    </div>
@endsection