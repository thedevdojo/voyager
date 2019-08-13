@extends('voyager::app')

@section('content')
<bread-edit-add
    :bread="{{ json_encode($bread) }}"
    action="{{ $data ? 'edit' : 'add' }}"
    :accessors="{{ json_encode($bread->getComputedProperties()) }}"
    :layout="{{ json_encode($layout) }}"
    :input="{{ json_encode($data) }}"
    :translatable="{{ json_encode($bread->getTranslatableFields()) }}"
    url="{{ isset($id) ? route('voyager.'.$bread->slug.'.update', $id) : route('voyager.'.$bread->slug.'.store') }}"
    prev-url="{{ url()->previous() }}"
></bread-edit-add>
@endsection
