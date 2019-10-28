@extends('voyager::app')

@section('content')

<settings-manager
    :input="{{ json_encode($settings) }}"
    url="{{ route('voyager.settings.store') }}"
></settings-manager>

@endsection