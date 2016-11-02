@extends('voyager::master')

@section('css')
    <link rel="stylesheet" href="{{ config('voyager.assets_path') }}/css/database.css">
@stop

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-data"></i> Database
        <a href="{{ route('voyager_database_create_table') }}" class="btn btn-success"><i class="fa fa-plus-circle"></i>
            Create New Table</a>
    </h1>
@stop

@section('page_header_actions')
    asdf
@stop

@section('content')

    <div class="page-content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <?php $dataTypes = TCG\Voyager\Models\DataType::all(); ?>
                <?php $dataTypeNames = array(); ?>
                @foreach($dataTypes as $type)
                    <?php array_push($dataTypeNames, $type->name); ?>
                @endforeach

                <table class="table table-striped database-tables">
                    <thead>
                    <tr>
                        <th>Table Name</th>
                        <th>BREAD/CRUD Actions</th>
                        <th style="text-align:right">Table Actions</th>
                    </tr>
                    </thead>

                    <?php $arr = DB::select('SHOW TABLES'); ?>
                    @foreach($arr as $a)
                        <?php $table = current($a); ?>
                        <?php $active = in_array($table, $dataTypeNames);
                        if ($active) {
                            $activeDataType = TCG\Voyager\Models\DataType::where('name', '=', $table)->first();
                        }
                        ?>
                        <tr>
                            <td>
                                <p class="name">
                                    @if($active)
                                        <a href="/admin/database/table/{{ $table }}" data-name="{{ $table }}"
                                           class="desctable">{{ $table }}</a> <i class="voyager-bread"
                                                                                 style="font-size:25px; position:absolute; margin-left:10px; margin-top:-3px;"></i>
                                    @else
                                        <a href="/admin/database/table/{{ $table }}" data-name="{{ $table }}"
                                           class="desctable">{{ $table }}</a>
                                    @endif
                                </p>
                            </td>

                            <td>

                                <div class="bread_actions">
                                    @if($active)
                                        <a class="btn-sm btn-default edit"
                                           href="/admin/database/{{ $activeDataType->id }}/edit-bread"> Edit BREAD</a>
                                        <div class="btn-sm btn-danger delete" style="display:inline"
                                             data-id="{{ $activeDataType->id }}" data-name="{{ $table }}"> Delete BREAD
                                        </div>
                                    @else
                                        <form action="/admin/database/create_bread" method="POST">
                                            <input type="hidden" value="{{ csrf_token() }}" name="_token">
                                            <input type="hidden" value="{{ $table }}" name="table">
                                            <button type="submit" class="btn-sm btn-default"><i
                                                        class="fa fa-plus-circle"></i> Add BREAD to this table
                                            </button>
                                        </form>
                                    @endif
                                </div>

                            </td>
                            <td class="actions">
                                <a class="btn-danger btn-sm pull-right delete_table @if($active) remove-bread-warning @endif"
                                   data-table="{{ $table }}" style="display:inline; cursor:pointer;"><i
                                            class="voyager-trash"></i> Delete</a>
                                <a class="btn-sm btn-primary pull-right" style="display:inline; margin-right:10px;"
                                   href="/admin/database/edit-{{ $table }}-table"><i class="voyager-edit"></i> Edit</a>
                                <a class="btn-sm btn-warning pull-right desctable"
                                   style="display:inline; margin-right:10px;" href="/admin/database/table/{{ $table }}"
                                   data-name="{{ $table }}"><i class="voyager-eye"></i> View</a>
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-trash"></i> Are you sure you want to delete the BREAD for
                        the <span id="delete_builder_name"></span> table?</h4>
                </div>
                <div class="modal-footer">
                    <form action="/admin/database/delete_bread" id="delete_builder_form" method="POST">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="submit" class="btn btn-danger" value="Yes, remove the BREAD">
                    </form>
                    <button type="button" class="btn btn-outline pull-right" data-dismiss="modal">Cancel</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-trash"></i> Are you sure you want to delete the <span
                                id="delete_table_name"></span> table?</h4>
                </div>
                <div class="modal-footer">
                    <form action="/admin/database/table/delete" id="delete_table_form" method="POST">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="submit" class="btn btn-danger pull-right" value="Yes, delete this table">
                        <button type="button" class="btn btn-outline pull-right" style="margin-right:10px;"
                                data-dismiss="modal">Cancel
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-data"></i> @{{ table.name }}</h4>
                </div>
                <div class="modal-body" style="overflow:scroll">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Field</th>
                            <th>Type</th>
                            <th>Null</th>
                            <th>Key</th>
                            <th>Default</th>
                            <th>Extra</th>
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
                    <button type="button" class="btn btn-outline pull-right" data-dismiss="modal">Close</button>
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
                id = $(e.target).data('id');
                name = $(e.target).data('name');

                $('#delete_builder_name').text(name);
                $('#delete_builder_form').attr('action', '/admin/database/delete_bread/' + id);
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
                            Field: val.Field,
                            Type: val.Type,
                            Null: val.Null,
                            Key: val.Key,
                            Default: val.Default,
                            Extra: val.Extra
                        });
                        $('#table_info').modal('show');
                    });
                });
            });

            $('td.actions').on('click', '.delete_table', function (e) {
                table = $(e.target).data('table');
                if ($(e.target).hasClass('remove-bread-warning')) {
                    toastr.warning("Please make sure to remove the BREAD on this table before deleting the table.");
                } else {
                    $('#delete_table_name').text(table);
                    $('#delete_table_form').attr('action', '/admin/database/table/delete/' + table);
                    $('#delete_modal').modal('show');
                }
            });


        });
    </script>

@stop