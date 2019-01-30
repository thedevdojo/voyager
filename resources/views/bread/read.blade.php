@extends('voyager::master')

@section('page_title', __('voyager::generic.view').' '.$dataType->display_name_singular)

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i> {{ __('voyager::generic.viewing') }} {{ ucfirst($dataType->display_name_singular) }} &nbsp;

        @include('voyager::bread.partials.read_actions')
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content read container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered" style="padding-bottom:5px;">
                    @include('voyager::bread.partials.read_rows')
                </div>
            </div>
        </div>
    </div>

    @include('voyager::bread.partials.read_modal-delete')
@stop

@include('voyager::bread.partials.read_scripts')
