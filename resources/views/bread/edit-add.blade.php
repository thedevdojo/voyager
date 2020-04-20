@extends('voyager::app')
@section('page-title', __('voyager::generic.'.($new ? 'add' : 'edit').'_type', ['type' => $bread->name_singular]))
@section('content')
<bread-edit-add
    :bread="{{ json_encode($bread) }}"
    action="{{ $new ? 'add' : 'edit' }}"
    :layout="{{ json_encode($layout) }}"
    :input="{{ $new ? '{}' : json_encode($data) }}"
    prev-url="{{ url()->previous() }}"
></bread-edit-add>
@endsection
