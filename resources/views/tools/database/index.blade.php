@extends('voyager::master')

@section('page_title', __('voyager.generic.viewing').' '.__('voyager.generic.database'))

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-data"></i> {{ __('voyager.generic.database') }}
        <a href="{{ route('voyager.database.create') }}" class="btn btn-success"><i class="voyager-plus"></i>
            {{ __('voyager.database.create_new_table') }}</a>
    </h1>
@stop

@section('content')

    <div class="page-content container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">

                <table class="table table-striped database-tables">
                    <thead>
                        <tr>
                            <th>{{ __('voyager.database.table_name') }}</th>
                            <th>{{ __('voyager.database.bread_crud_actions') }}</th>
                            <th style="text-align:right">{{ __('voyager.database.table_actions') }}</th>
                        </tr>
                    </thead>

            @foreach($tables as $table)
                    @continue(in_array($table->name, config('voyager.database.tables.hidden', [])))
                    <tr>
                        <td>
                            <p class="name">
                                <a href="{{ route('voyager.database.show', $table->name) }}"
                                   data-name="{{ $table->name }}" class="desctable">
                                   {{ $table->name }}
                                </a>
                            @if($table->dataTypeId)
                                <i class="voyager-bread"
                                   style="font-size:25px; position:absolute; margin-left:10px; margin-top:-3px;"></i>
                            @endif
                            </p>
                        </td>

                        <td>
                            <div class="bread_actions">
                            @if($table->dataTypeId)
                                <a href="{{ route('voyager.' . $table->slug . '.index') }}"
                                   class="btn-sm btn-warning browse_bread">
                                    <i class="voyager-plus"></i> {{ __('voyager.database.browse_bread') }}
                                </a>
                                <a href="{{ route('voyager.database.bread.edit', $table->name) }}"
                                   class="btn-sm btn-default edit">
                                   {{ __('voyager.database.edit_bread') }}
                                </a>
                                <div data-id="{{ $table->dataTypeId }}" data-name="{{ $table->name }}"
                                     class="btn-sm btn-danger delete" style="display:inline">
                                     {{ __('voyager.database.delete_bread') }}
                                </div>
                            @else
                                <a href="{{ route('voyager.database.bread.create', ['name' => $table->name]) }}"
                                   class="btn-sm btn-default">
                                    <i class="voyager-plus"></i> {{ __('voyager.database.add_bread') }}
                                </a>
                            @endif
                            </div>
                        </td>

                        <td class="actions">
                            <a class="btn btn-danger btn-sm pull-right delete_table @if($table->dataTypeId) remove-bread-warning @endif"
                               data-table="{{ $table->name }}" style="display:inline; cursor:pointer;">
                               <i class="voyager-trash"></i> {{ __('voyager.generic.delete') }}
                            </a>
                            <a href="{{ route('voyager.database.edit', $table->name) }}"
                               class="btn btn-sm btn-primary pull-right" style="display:inline; margin-right:10px;">
                               <i class="voyager-edit"></i> {{ __('voyager.generic.edit') }}
                            </a>
                            <a href="{{ route('voyager.database.show', $table->name) }}"
                               data-name="{{ $table->name }}"
                               class="btn btn-sm btn-warning pull-right desctable" style="display:inline; margin-right:10px;">
                               <i class="voyager-eye"></i> {{ __('voyager.generic.view') }}
                            </a>
                        </td>
                    </tr>
                @endforeach
                </table>
            </div>
        </div>
    </div>

    <div class="modal modal-danger fade" tabindex="-1" id="delete_builder_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager.generic.close') }}"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-trash"></i>  {!! __('voyager.database.delete_table_bread_quest', ['table' => '<span id="delete_builder_name"></span>']) !!}</h4>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('voyager.database.bread.delete', ['id' => null]) }}" id="delete_builder_form" method="POST">
                        {{ method_field('DELETE') }}
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="submit" class="btn btn-danger" value="{{ __('voyager.database.delete_table_bread_conf') }}">
                    </form>
                    <button type="button" class="btn btn-outline pull-right" data-dismiss="modal">{{ __('voyager.generic.cancel') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager.generic.close') }}"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-trash"></i> {!! __('voyager.database.delete_table_bread_quest', ['table' => '<span id="delete_table_name"></span>']) !!}</h4>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('voyager.database.destroy', ['database' => '__database']) }}" id="delete_table_form" method="POST">
                        {{ method_field('DELETE') }}
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="submit" class="btn btn-danger pull-right" value="{{ __('voyager.database.delete_table_confirm') }}">
                        <button type="button" class="btn btn-outline pull-right" style="margin-right:10px;"
                                data-dismiss="modal">{{ __('voyager.generic.cancel') }}
                        </button>
                    </form>

                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal modal-info fade" tabindex="-1" id="table_info" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager.generic.close') }}"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-data"></i> @{{ table.name }}</h4>
                </div>
                <div class="modal-body" style="overflow:scroll">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>{{ __('voyager.database.field') }}</th>
                            <th>{{ __('voyager.database.type') }}</th>
                            <th>{{ __('voyager.database.null') }}</th>
                            <th>{{ __('voyager.database.key') }}</th>
                            <th>{{ __('voyager.database.default') }}</th>
                            <th>{{ __('voyager.database.extra') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="row in table.rows">
                            <td><strong>@{{ row.Field }}</strong></td>
                            <td>@{{ row.Type }}</td>
                            <td>@{{ row.Null }}</td>
                            <td>@{{ row.Key }}</td>
                            <td>@{{ row.Default }}</td>
                            <td>@{{ row.Extra }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline pull-right" data-dismiss="modal">{{ __('voyager.generic.close') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

@stop

@section('javascript')

    <script>

        var table = {
            name: '',
            rows: []
        };

        new Vue({
            el: '#table_info',
            data: {
                table: table,
            },
        });

        $(function () {

            $('.bread_actions').on('click', '.delete', function (e) {
                id = $(this).data('id');
                name = $(this).data('name');

                $('#delete_builder_name').text(name);
                $('#delete_builder_form')[0].action += '/' + id;
                $('#delete_builder_modal').modal('show');
            });

            $('.database-tables').on('click', '.desctable', function (e) {
                e.preventDefault();
                href = $(this).attr('href');
                table.name = $(this).data('name');
                table.rows = [];
                $.get(href, function (data) {
                    $.each(data, function (key, val) {
                        table.rows.push({
                            Field: val.field,
                            Type: val.type,
                            Null: val.null,
                            Key: val.key,
                            Default: val.default,
                            Extra: val.extra
                        });
                        $('#table_info').modal('show');
                    });
                });
            });

            $('td.actions').on('click', '.delete_table', function (e) {
                table = $(this).data('table');
                if ($(this).hasClass('remove-bread-warning')) {
                    toastr.warning('{{ __('voyager.database.delete_bread_before_table') }}');
                } else {
                    $('#delete_table_name').text(table);
                    $('#delete_table_form')[0].action = $('#delete_table_form')[0].action.replace('__database', table);
                    $('#delete_modal').modal('show');
                }
            });

        });
    </script>

@stop
