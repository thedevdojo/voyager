@can('add', app($dataType->model_name))
    <a href="{{ route('voyager.'.$dataType->slug.'.create') }}" class="btn btn-success btn-add-new">
        <i class="voyager-plus"></i> <span>{{ __('voyager::generic.add_new') }}</span>
    </a>
@endcan
@can('delete', app($dataType->model_name))
    @include('voyager::partials.bulk-delete')
@endcan
@can('edit', app($dataType->model_name))
    @if(isset($dataType->order_column) && isset($dataType->order_display_column))
        <a href="{{ route('voyager.'.$dataType->slug.'.order') }}" class="btn btn-primary">
            <i class="voyager-list"></i> <span>{{ __('voyager::bread.order') }}</span>
        </a>
    @endif
@endcan