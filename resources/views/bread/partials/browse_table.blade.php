<table id="dataTable" class="table table-hover">
    <thead>
        <tr>
            @can('delete',app($dataType->model_name))
                <th>
                    <input type="checkbox" class="select_all">
                </th>
            @endcan

            @include('voyager::bread.partials.browse_table_header')

            <th class="actions text-right">{{ __('voyager::generic.actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($dataTypeContent as $data)
        <tr>
            @can('delete',app($dataType->model_name))
                <td>
                    <input type="checkbox" name="row_id" id="checkbox_{{ $data->getKey() }}" value="{{ $data->getKey() }}">
                </td>
            @endcan

            @include('voyager::bread.partials.browse_table_body')

            <td class="no-sort no-click" id="bread-actions">
                @foreach(Voyager::actions() as $action)
                    @include('voyager::bread.partials.browse_row_actions', ['action' => $action])
                @endforeach
            </td>
        </tr>
        @endforeach
    </tbody>
</table>