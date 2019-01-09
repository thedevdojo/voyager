@extends('voyager::master')

@section('page_title', __('voyager::generic.media'))

@section('content')
    <div class="page-content container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">

                <div class="admin-section-title">
                    <h3><i class="voyager-images"></i> {{ __('voyager::generic.media') }}</h3>
                </div>
                <div class="clear"></div>
                <div id="filemanager">
                    <media-manager base-path="{{ config('voyager.media.path', '/') }}"></media-manager>
                </div>
            </div><!-- .row -->
        </div><!-- .col-md-12 -->
    </div><!-- .page-content container-fluid -->
@stop

@section('javascript')
    @include('voyager::media.manager')
    <script>
    new Vue({
        el: '#filemanager'
    });
    </script>
@endsection
