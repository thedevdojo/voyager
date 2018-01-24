@extends('voyager::master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ voyager_asset('css/ga-embed.css') }}">
    <style>
        .user-email {
            font-size: .85rem;
            margin-bottom: 1.5em;
        }
    </style>
@stop

@section('content')
    <div style="background-size:cover; background-image: url({{ Voyager::image( Voyager::setting('admin.bg_image'), config('voyager.assets_path') . '/images/bg.jpg') }}); background-position: center center;position:absolute; top:0; left:0; width:100%; height:300px;"></div>
    <div style="height:160px; display:block; width:100%"></div>
    <div style="position:relative; z-index:9; text-align:center;">
        <img src="@if( !filter_var(voyager_auth()->user()->avatar, FILTER_VALIDATE_URL)){{ Voyager::image( voyager_auth()->user()->avatar ) }}@else{{ voyager_auth()->user()->avatar }}@endif"
             class="avatar"
             style="border-radius:50%; width:150px; height:150px; border:5px solid #fff;"
             alt="{{ voyager_auth()->user()->name }} avatar">
        <h4>{{ ucwords(voyager_auth()->user()->name) }}</h4>
        <div class="user-email text-muted">{{ ucwords(voyager_auth()->user()->email) }}</div>
        <p>{{ voyager_auth()->user()->bio }}</p>
        <a href="{{ route('voyager.users.edit', voyager_auth()->user()->id) }}" class="btn btn-primary">{{ __('voyager.profile.edit') }}</a>
    </div>
@stop
