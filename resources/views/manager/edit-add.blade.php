@extends('voyager::app')

@section('content')
    <locale-picker></locale-picker>
    <br>
    <bread-builder
        :data="{{ json_encode($bread) }}"
        :computed="{{ json_encode($bread->getComputedProperties()) }}"
        :relationships="{{ json_encode($bread->getRelationships(true)) }}"
        :fields="{{ json_encode($bread->getFields()) }}"
        url="{{ route('voyager.bread.update', $bread->table) }}"
    />
@endsection