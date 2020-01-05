@extends('voyager::master')

@section('page_title', $dataType->getTranslatedAttribute('display_name_plural') . ' ' . __('voyager::bread.order'))

@section('page_header')
<h1 class="page-title">
    <i class="voyager-list"></i>{{ $dataType->getTranslatedAttribute('display_name_plural') }} {{ __('voyager::bread.order') }}
</h1>
@stop

@section('content')
<div class="page-content container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <p class="panel-title" style="color:#777">{{ __('voyager::generic.drag_drop_info') }}</p>
                </div>

                <div class="panel-body" style="padding:30px;">
                    <div class="dd">
                        @include('voyager::bread.partials.order', ['items' => $results, 'nestable' => $nestable, 'parentId' => ''])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('javascript')

<script>
$(document).ready(function () {
    $('.dd').nestable({
        @if(empty($nestable)) maxDepth: 1 @endif
    });

    /**
    * Reorder items
    */
    $('.dd').on('change', function (e) {
        $.post('{{ route('voyager.'.$dataType->slug.'.order') }}', {
            order: JSON.stringify($('.dd').nestable('serialize')),
            _token: '{{ csrf_token() }}'
        }, function (data) {
            toastr.success("{{ __('voyager::bread.updated_order') }}");
        });
    });
});
</script>
@stop
