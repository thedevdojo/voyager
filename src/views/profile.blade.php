@extends('voyager::master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ config('voyager.assets_path') }}/css/ga-embed.css">
@stop

@section('content')
	<div style="background-image:url({{ config('voyager.assets_path') }}/images/bg.jpg); background-size:cover; background-position:center center; position:absolute; top:0px; left:0px; width:100%; height:300px;"></div>
	<div style="height:160px; display:block; width:100%"></div>
	<div style="position:relative; z-index:9; text-align:center;">
		<img src="{{ Voyager::image( Auth::user()->avatar ) }}" class="avatar" style="border-radius:50%; width:150px; height:150px; border:5px solid #fff;" alt="{{ Auth::user()->name }} avatar">
        <h4>{{ ucwords(Auth::user()->name) }}</h4>
        <p>{{ Auth::user()->bio }}</p>
        <a href="/admin/users/{{ Auth::user()->id }}/edit" class="btn btn-primary">Edit My Profile</a>
    </div>
@stop