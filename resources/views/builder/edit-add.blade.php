@extends('voyager::app')
@section('page-title', __('voyager::generic.'.($new ? 'add' : 'edit').'_type', ['type' => __('voyager::generic.bread')]))
@section('content')
    <bread-builder-edit-add :data="{{ json_encode($bread) }}" :is-new="{{ $new ? 'true' : 'false' }}" />
@endsection