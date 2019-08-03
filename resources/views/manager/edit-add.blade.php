@extends('voyager::app')

@section('content')
    <locale-picker></locale-picker>
    <br>
    <bread-builder
        :data="{{ json_encode($bread) }}"
        :fields="{{ json_encode($fields) }}"
        url="{{ route('voyager.bread.update', $bread->table) }}"
    />
@endsection

@section('js')

@endsection