@extends('voyager::app')

@section('content')
    <locale-picker></locale-picker>
    <br>
    <bread-edit-add
        :data="{{ json_encode($bread) }}"
        :fields="{{ json_encode($fields) }}"
        url="{{ route('voyager.bread.update', $bread->table) }}"
    ></bread-builder>
@endsection

@section('js')

@endsection