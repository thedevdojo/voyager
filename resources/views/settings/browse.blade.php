@extends('voyager::app')
@section('page-title', __('voyager::generic.settings'))
@section('content')

<settings-manager
    :input="{{ $settings->toJson() }}"
    :formfields="{{ json_encode(Bread::getFormfields()) }}"
></settings-manager>

@endsection