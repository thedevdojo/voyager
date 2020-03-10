@extends('voyager::app')
@section('page-title', __('voyager::manager.breads'))
@section('content')
    <bread-builder-browse
        :tables="{{ json_encode($tables) }}"
        :breads="{{ json_encode(Bread::getBreads()) }}"
    />
@endsection
