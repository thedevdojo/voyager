@extends('voyager::app')
@section('page-title', __('voyager::bread.browse_type', ['type' => $bread->name_plural]))
@section('content')
<bread-browse
    :bread="{{ json_encode($bread) }}"
    :accessors="{{ json_encode($bread->getComputedProperties()) }}"
    :layout="{{ json_encode($layout) }}"
    :actions="{{ $actions }}"
></bread-browse>
@endsection
