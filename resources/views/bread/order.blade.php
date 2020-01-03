@extends('voyager::master')

@section('page_title', $dataType->getTranslatedAttribute('display_name_plural') . ' ' . __('voyager::bread.order'))

@section('page_header')
<h1 class="page-title">
    <i class="voyager-list"></i>{{ $dataType->getTranslatedAttribute('display_name_plural') }} {{ __('voyager::bread.order') }}
</h1>
@if ($results_tree_view)
<button type="button" id="collapse-all" class="btn btn-primary">Collapse All</button>
<button type="button" id="expand-all" class="btn btn-primary">Expand All</button>
@endif
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
                        <ol class="dd-list">
                            @if ($results_tree_view)
                                <!-- Order tree ON -->
                                {!! $results !!}
                            @else
                                <!-- Order tree OFF -->
                                {!! $results !!}
                            @endif
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('javascript')

<!-- Reorder items -->

@if ($results_tree_view)

    <!-- Order tree ON -->
    <script>
        $(document).ready(function () {
            $('.dd').nestable({
                maxDepth: 10
            });
        });
        $( document ).on( "click", "#collapse-all", function() {
            $('.dd').nestable('collapseAll');
        });
        $( document ).on( "click", "#expand-all", function() {
            $('.dd').nestable('expandAll');
        });
    </script>

@else

    <!-- Order tree OFF -->
    <script>
        $(document).ready(function () {
            $('.dd').nestable({
                maxDepth: 1
            });
        });
    </script>

@endif

<!-- Order tree Always -->
<script>
    $(document).ready(function () {
        $('.dd').on('change', function (e) {
            $.post('{{ route('voyager.'.$dataType->slug.'.order') }}', {
                order: JSON.stringify($('.dd').nestable('serialize')),
                orderDirection: "{{ $dataType->order_direction }}",
                _token: '{{ csrf_token() }}'
            }, function (data) {
                toastr.success("{{ __('voyager::bread.updated_order') }}");
            });
        });
    });
</script>

@stop
