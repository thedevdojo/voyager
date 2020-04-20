@extends('voyager::app')
@section('page-title', __('voyager::generic.'.($new ? 'add' : 'edit').'_type', ['type' => __('voyager::generic.bread')]))
@section('content')
    <bread-manager-edit-add :data="{{ json_encode($bread) }}" :formfields="{{ json_encode(Bread::getFormfields()) }}"/>
@endsection