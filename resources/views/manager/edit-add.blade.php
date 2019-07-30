@extends('voyager::app')

@section('content')
    <locale-picker></locale-picker>
    <br>
    <bread-builder :bread="{{ json_encode($bread) }}" url="{{ route('voyager.bread.update', $bread->table) }}"></bread-builder>
@endsection

@section('js')

@endsection