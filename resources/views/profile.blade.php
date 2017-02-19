@extends('voyager::master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ config('voyager.assets_path') }}/css/ga-embed.css">
    <style>
        .user-email {
            font-size: .85rem;
            margin-bottom: 1.5em;
        }
    </style>
@stop

@section('content')
    <div style="background-size:cover; background: url({{ Voyager::image( Voyager::setting('admin_bg_image'), config('voyager.assets_path') . '/images/bg.jpg') }}) center center;position:absolute; top:0; left:0; width:100%; height:300px;"></div>
    <div style="height:160px; display:block; width:100%"></div>
    <div style="position:relative; z-index:9; text-align:center;">
        <img src="@if( strpos(auth()->user()->avatar, 'http://') === false && strpos(auth()->user()->avatar, 'https://') === false){{ Voyager::image( auth()->user()->avatar ) }}@else{{ auth()->user()->avatar }}@endif" class="avatar"
             style="border-radius:50%; width:150px; height:150px; border:5px solid #fff;"
             alt="{{ auth()->user()->name }} avatar">
        <h4>{{ ucwords(auth()->user()->name) }}</h4>
        <div class="user-email text-muted">{{ ucwords(auth()->user()->email) }}</div>
        <p>{{ auth()->user()->bio }}</p>
        <a href="{{ route('voyager.users.edit', auth()->user()->id) }}" class="btn btn-primary">Edit My Profile</a>
    </div>
@stop
