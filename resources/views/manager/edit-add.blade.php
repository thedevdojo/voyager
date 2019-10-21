@extends('voyager::app')

@section('content')
    <bread-builder
        :data="{{ json_encode($bread) }}"
        :computed="{{ json_encode($bread->getComputedProperties()) }}"
        :relationships="{{ json_encode($bread->getRelationships(true)) }}"
        :columns="{{ json_encode($bread->getColumns()) }}"
        url="{{ route('voyager.bread.update', $bread->table) }}"
    />
@endsection