@extends('voyager::master')

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-data"></i>
        @if(isset($table))
            {{ "Editing $table->name table" }}
        @else
            {{ 'New Table' }}
        @endif
    </h1>
@stop

@section('content')

    <div class="page-content container-fluid">
        <div class="row">
            <div id="tableEditor" class="col-md-12">
                <form @submit="stringifyTable" action="{{ $formAction }}" method="POST">
                    @if(isset($table)){{ method_field('PUT') }}@endif

                    <database-table-editor :table="table"></database-table-editor>

                    <input type="hidden" :value="tableJson" name="table">

                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </form>
            </div>
        </div>
    </div>

@stop

@section('javascript')
    @include('voyager::tools.database.vue-components.database-table-editor')

    <script>
        new Vue({
            el: '#tableEditor',
            data: {
                table: {!! $table->toJson() !!},
                tableJson: ''
                // originalTable: {} // to do comparison later?
            },
            methods: {
                stringifyTable() {
                    this.tableJson = JSON.stringify(this.table);
                }
            }
        });
    </script>

@stop
