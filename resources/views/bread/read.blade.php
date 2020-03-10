@extends('voyager::app')
@section('page-title', __('voyager::generic.show_type', ['type' => $bread->name_singular]))
@section('content')
<bread-read
    :bread="{{ json_encode($bread) }}"
    :accessors="{{ json_encode($bread->getComputedProperties()) }}"
    :layout="{{ json_encode($layout) }}"
    :input="{{ json_encode($data) }}"
    prev-url="{{ url()->previous() }}"
></bread-edit-add>
@endsection
