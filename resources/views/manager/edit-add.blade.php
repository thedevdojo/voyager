@extends('voyager::app')
@section('page-title', __('voyager::generic.'.($new ? 'add' : 'edit').'_type', ['type' => __('voyager::bread.bread')]))
@section('content')
    <bread-builder
        :data="{{ json_encode($bread) }}"
        :computed="{{ json_encode($bread->getComputedProperties()) }}"
        :relationships="{{ json_encode($bread->getRelationships()) }}"
        :columns="{{ json_encode($bread->getColumns()) }}"
        :scopes="{{ json_encode($bread->getScopes()) }}"
        :new="{{ $new ? 'true' : 'false' }}"
        url="{{ route('voyager.bread.update', $bread->table) }}"
    />
@endsection