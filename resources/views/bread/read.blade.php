@extends('voyager::app')
@section('page-title', __('voyager::generic.show_type', ['type' => $bread->name_singular]))
@section('content')
<bread-read
    :bread="{{ json_encode($bread) }}"
    :layout="{{ json_encode($layout) }}"
    :data="{{ json_encode($data) }}"
    :primary="{{ $data->getKey() }}"
    :input="{{ json_encode($data) }}"
    prev-url="{{ url()->previous() }}"
></bread-edit-add>
@endsection
