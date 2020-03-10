@extends('voyager::app')
@section('page-title', __('voyager::generic.'.(isset($id) ? 'edit' : 'add').'_type', ['type' => $bread->name_singular]))
@section('content')
<bread-edit-add
    :bread="{{ json_encode($bread) }}"
    action="{{ isset($id) ? 'edit' : 'add' }}"
    :accessors="{{ json_encode($bread->getComputedProperties()) }}"
    :relationships="{{ json_encode($bread->getRelationships()) }}"
    :layout="{{ json_encode($layout) }}"
    :input="{{ json_encode($data) }}"
    :errors="{{ json_encode($errors ?? new stdClass) }}"
    url="{{ isset($id) ? route('voyager.'.$bread->slug.'.update', $id) : route('voyager.'.$bread->slug.'.store') }}"
    prev-url="{{ url()->previous() }}"
></bread-edit-add>
@endsection
