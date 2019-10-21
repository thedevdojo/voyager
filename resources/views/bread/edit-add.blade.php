@extends('voyager::app')

@section('content')
<bread-edit-add
    :bread="{{ json_encode($bread) }}"
    action="{{ isset($id) ? 'edit' : 'add' }}"
    :accessors="{{ json_encode($bread->getComputedProperties()) }}"
    :layout="{{ json_encode($layout) }}"
    :input="{{ json_encode($data) }}"
    :errors="{{ json_encode($errors ?? new stdClass) }}"
    :translatable="{{ json_encode($bread->getTranslatableColumns()) }}"
    url="{{ isset($id) ? route('voyager.'.$bread->slug.'.update', $id) : route('voyager.'.$bread->slug.'.store') }}"
    prev-url="{{ url()->previous() }}"
></bread-edit-add>
@endsection
