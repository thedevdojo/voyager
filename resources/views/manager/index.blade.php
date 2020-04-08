@extends('voyager::app')
@section('page-title', __('voyager::generic.breads'))
@section('content')
    <bread-manager-browse :tables="{{ json_encode($tables) }}" />
@endsection
