@extends('voyager::app')

@section('content')
    <locale-picker></locale-picker>
    <br>
    <bread-builder :bread="{{ json_encode($bread) }}"></bread-builder>
@endsection

@section('js')

@endsection