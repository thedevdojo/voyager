@extends('voyager::app')
@section('page-title', __('voyager::generic.breads'))
@section('content')
    <bread-builder-browse :tables="{{ json_encode($tables) }}" />
@endsection
