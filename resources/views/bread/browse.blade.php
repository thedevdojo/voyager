@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing').' '.$dataType->display_name_plural)

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="{{ $dataType->icon }}"></i> {{ $dataType->display_name_plural }}
        </h1>
        @include('voyager::bread.partials.browse_actions')
        @include('voyager::multilingual.language-selector')
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        @include('voyager::bread.partials.browse_search')
                        <div class="table-responsive">
                            @include('voyager::bread.partials.browse_table')
                        </div>
                        @include('voyager::bread.partials.browse_footer')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('voyager::bread.partials.browse_modal-delete')
@stop

@include('voyager::bread.partials.browse_scripts')