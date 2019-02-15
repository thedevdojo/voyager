@extends('voyager::master')

@section('css')
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
        <img src="@if( !filter_var(Auth::guard('admin')->user()->avatar, FILTER_VALIDATE_URL)){{ Voyager::image( Auth::guard('admin')->user()->avatar ) }}@else{{ Auth::guard('admin')->user()->avatar }}@endif"
             class="avatar"
             style="border-radius:50%; width:150px; height:150px; border:5px solid #fff;"
             alt="{{ Auth::guard('admin')->user()->name }} avatar">
        <h4>{{ ucwords(Auth::guard('admin')->user()->name) }}</h4>
        <div class="user-email text-muted">{{ ucwords(Auth::guard('admin')->user()->email) }}</div>
        <p>{{ Auth::guard('admin')->user()->bio }}</p>
        <a href="{{ route('voyager.users.edit', Auth::guard('admin')->user()->id) }}" class="btn btn-primary">{{ __('voyager::profile.edit') }}</a>
    </div>
@stop
