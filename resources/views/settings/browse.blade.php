@extends('voyager::app')
@section('page-title', __('voyager::generic.settings'))
@section('content')

<settings-manager
    :input="{{ $settings->toJson() }}"
></settings-manager>

@endsection