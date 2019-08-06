@extends('voyager::app')

@section('content')
    <locale-picker></locale-picker>
    <br>
    <bread-builder
        :data="{{ json_encode($bread) }}"
        :accessors="{{ json_encode($bread->getAccessors()) }}"
        :relationships="{{ json_encode($bread->getRelationships(true)) }}"
        :fields="{{ json_encode($bread->getFields()) }}"
        url="{{ route('voyager.bread.update', $bread->table) }}"
    />
@endsection

@section('js')

@endsection