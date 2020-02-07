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
    <div style="background-size:cover; background-image: url({{ Voyager::image( Voyager::setting('admin.bg_image'), voyager_asset('/images/bg.jpg')) }}); background-position: center center;position:absolute; top:0; left:0; width:100%; height:300px;"></div>
    <div style="height:160px; display:block; width:100%"></div>
    <div style="position:relative; z-index:9; text-align:center;">
        <img src="@if( !filter_var(Auth::user()->avatar, FILTER_VALIDATE_URL)){{ Voyager::image( Auth::user()->avatar ) }}@else{{ Auth::user()->avatar }}@endif"
             class="avatar"
             style="border-radius:50%; width:150px; height:150px; border:5px solid #fff;"
             alt="{{ Auth::user()->name }} avatar">
        <h4>{{ ucwords(Auth::user()->name) }}</h4>
        <div class="user-email text-muted">{{ ucwords(Auth::user()->email) }}</div>
        <p>{{ Auth::user()->bio }}</p>
        @if ($route != '')
            <a href="{{ $route }}" class="btn btn-primary">{{ __('voyager::profile.edit') }}</a>
        @endif
        @if ($mfa != '')
            <a href="{{ $mfa }}" class="btn btn-primary" data-toggle="modal" data-target="#mfa-code">{{ __('voyager::profile.view_code') }}</a>
        @endif
    </div>

    <div class="modal fade" id="mfa-code" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document" style="width:260px">
            <div class="modal-content">
            <div class="modal-body text-center">
                <img class="mfa" src="{{ $mfa }}" alt="{{ Auth::user()->name }} mfa code">
            </div>
        </div>
    </div>
@stop
