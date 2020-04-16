@extends('voyager::app')
@section('page-title', __('voyager::bread.browse_type', ['type' => $bread->name_plural]))
@section('content')
<bread-browse
    :bread="{{ json_encode($bread) }}"
></bread-browse>
@endsection
