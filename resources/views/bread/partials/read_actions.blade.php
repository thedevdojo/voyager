@can('edit', $dataTypeContent)
    <a href="{{ route('voyager.'.$dataType->slug.'.edit', $dataTypeContent->getKey()) }}" class="btn btn-info">
        <span class="glyphicon glyphicon-pencil"></span>&nbsp;
        {{ __('voyager::generic.edit') }}
    </a>
@endcan
@can('delete', $dataTypeContent)
    <a href="javascript:;" title="{{ __('voyager::generic.delete') }}" class="btn btn-danger" data-id="{{ $dataTypeContent->getKey() }}" id="delete-{{ $dataTypeContent->getKey() }}">
        <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">{{ __('voyager::generic.delete') }}</span>
    </a>
@endcan

<a href="{{ route('voyager.'.$dataType->slug.'.index') }}" class="btn btn-warning">
    <span class="glyphicon glyphicon-list"></span>&nbsp;
    {{ __('voyager::generic.return_to_list') }}
</a>