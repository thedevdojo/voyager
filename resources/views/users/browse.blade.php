@extends('voyager::master')

@section('page_title', __('voyager.generic.viewing').' '.$dataType->display_name_plural)

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i> {{ $dataType->display_name_plural }}
        @if (Voyager::can('add_'.$dataType->name))
            <a href="{{ route('voyager.'.$dataType->slug.'.create') }}" class="btn btn-success">
                <i class="voyager-plus"></i> {{ __('voyager.generic.add_new') }}
            </a>
        @endif
    </h1>
@stop

@section('content')
    <div class="page-content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <table id="dataTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('voyager.generic.name') }}</th>
                                    <th>{{ __('voyager.generic.email') }}</th>
                                    <th>{{ __('voyager.generic.created_at') }}</th>
                                    <th>{{ __('voyager.profile.avatar') }}</th>
                                    <th>{{ __('voyager.profile.role') }}</th>
                                    <th class="actions">{{ __('voyager.generic.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($dataTypeContent as $data)
                                <tr>
                                    <td>{{ucwords($data->name)}}</td>
                                    <td>{{$data->email}}</td>
                                    <td>{{ \Carbon\Carbon::parse($data->created_at)->format('F jS, Y h:i A') }}</td>
                                    <td>
                                        <img src="@if( !filter_var($data->avatar, FILTER_VALIDATE_URL)){{ Voyager::image( $data->avatar ) }}@else{{ $data->avatar }}@endif" style="width:100px">
                                    </td>
                                    <td>{{ $data->role ? $data->role->display_name : '' }}</td>
                                    <td class="no-sort no-click">
                                        @php $primaryKey = isset($data->primaryKey) ? $data->primaryKey : $data->id @endphp
                                        @if (Voyager::can('delete_'.$dataType->name))
                                            <div class="btn-sm btn-danger pull-right delete" data-id="{{ $primaryKey }}" id="delete-{{ $primaryKey }}">
                                                <i class="voyager-trash"></i> {{ __('voyager.generic.delete') }}
                                            </div>
                                        @endif
                                        @if (Voyager::can('edit_'.$dataType->name))
                                            <a href="{{ route('voyager.'.$dataType->slug.'.edit', $primaryKey) }}" class="btn-sm btn-primary pull-right edit">
                                                <i class="voyager-edit"></i> {{ __('voyager.generic.edit') }}
                                            </a>
                                        @endif
                                        @if (Voyager::can('read_'.$dataType->name))
                                            <a href="{{ route('voyager.'.$dataType->slug.'.show', $primaryKey) }}" class="btn-sm btn-warning pull-right">
                                                <i class="voyager-eye"></i> {{ __('voyager.generic.view') }}
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @if (isset($dataType->server_side) && $dataType->server_side)
                            <div class="pull-left">
                                <div role="status" class="show-res" aria-live="polite">{{ __('voyager.generic.showing_entries', $dataTypeContent->total(), ['from' => $dataTypeContent->firstItem(), 'to' => $dataTypeContent->lastItem(), 'all' => $dataTypeContent->total()]) }}</div>
                            </div>
                            <div class="pull-right">
                                {{ $dataTypeContent->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager.generic.close') }}"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-trash"></i> {{ __('voyager.generic.delete_question') }} {{ $dataType->display_name_singular }}?</h4>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('voyager.'.$dataType->slug.'.index') }}" id="delete_form" method="POST">
                        {{ method_field("DELETE") }}
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-danger pull-right delete-confirm"
                                 value="Yes, Delete This {{ $dataType->display_name_singular }}">
                    </form>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager.generic.cancel') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@stop

@section('javascript')
    <!-- DataTables -->
    <script>
        $(document).ready(function () {
            @if (!$dataType->server_side)
                $('#dataTable').DataTable({
                    "order": [],
                    "language": {!! json_encode(__('voyager.datatable'), true) !!}
                    @if(config('dashboard.data_tables.responsive')), responsive: true @endif
                });
            @endif

            $('td').on('click', '.delete', function (e) {
                var form = $('#delete_form')[0];

                form.action = parseActionUrl(form.action, $(this).data('id'));

                $('#delete_modal').modal('show');
            });

            function parseActionUrl(action, id) {
                return action.match(/\/[0-9]+$/)
                        ? action.replace(/([0-9]+$)/, id)
                        : action + '/' + id;
            }
        });
    </script>
@stop
