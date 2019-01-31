@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('page_title', __('voyager::generic.'.(!is_null($dataTypeContent->getKey()) ? 'edit' : 'add')).' '.$dataType->display_name_singular)

@section('page_header')
    @section('edit-add-page-title')
        <h1 class="page-title">
            <i class="{{ $dataType->icon }}"></i>
            {{ __('voyager::generic.'.(!is_null($dataTypeContent->getKey()) ? 'edit' : 'add')).' '.$dataType->display_name_singular }}
        </h1>
    @show

    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    @include('voyager::bread.partials.edit-add_form')
                </div>
            </div>
        </div>
    </div>

    @include('voyager::bread.partials.edit-add_modal-delete')
@stop

@include('voyager::bread.partials.edit-add_scripts')
