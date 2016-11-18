@extends('voyager::master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ config('voyager.assets_path') }}/css/ga-embed.css">
@stop

@section('content')
    <div style="background-size:cover; background: url({{ Voyager::image( Voyager::setting('admin_bg_image'), config('voyager.assets_path') . '/images/bg.jpg') }}) center center;position:absolute; top:0; left:0; width:100%; height:300px;"></div>
    <div style="height:160px; display:block; width:100%"></div>
    <div style="position:relative; z-index:9; text-align:center;">
        <img src="{{ Voyager::image( Auth::user()->avatar ) }}" class="avatar"
             style="border-radius:50%; width:150px; height:150px; border:5px solid #fff;"
             alt="{{ Auth::user()->name }} avatar">
        <h4>{{ ucwords(Auth::user()->name) }}</h4>
        <p>{{ Auth::user()->bio }}</p>
        <a href="{{ route('users.edit', Auth::user()->id) }}" class="btn btn-primary">Edit My Profile</a>
    </div>
@stop