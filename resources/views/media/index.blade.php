@extends('voyager::master')

@section('page_title', __('voyager::generic.media'))

@section('content')
    @include('voyager::media.filemanager')
@stop
