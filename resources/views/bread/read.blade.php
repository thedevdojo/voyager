@extends('voyager::app')

@section('content')
<bread-read
    :bread="{{ json_encode($bread) }}"
    :accessors="{{ json_encode($bread->getComputedProperties()) }}"
    :layout="{{ json_encode($layout) }}"
    :input="{{ json_encode($data) }}"
    :translatable="{{ json_encode($bread->getTranslatableFields()) }}"
    prev-url="{{ url()->previous() }}"
></bread-edit-add>
@endsection
