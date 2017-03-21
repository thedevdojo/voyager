@section('database-table-editor-template')

<div class="panel panel-bordered">
    <div class="panel-heading">
        <h3 class="panel-title">@if($db->action == 'update'){{ "Edit the {$db->table->name} table below" }}@else{{ 'Create Your New Table Below' }}@endif</h3>
    </div>

    <div class="panel-body">
        <div class="row">
        @if($db->action == 'update')
            <div class="col-md-12">
        @else
            <div class="col-md-6">
        @endif
                <label for="name">Table Name</label><br>
                <input v-model.trim="table.name" type="text" class="form-control" placeholder="Table Name" required pattern="{{ $db->identifierRegex }}">
            </div>

        @if($db->action == 'create')
            <div class="col-md-3 col-sm-4 col-xs-6">
                <label for="create_model">Create model for this table?</label><br>
                <input type="checkbox" name="create_model" data-toggle="toggle"
                       data-on="Yes, Please" data-off="No Thanks">
            </div>

            <div class="col-md-3 col-sm-4 col-xs-6">
                <label for="create_migration">Create migration for this table?</label><br>
                <input disabled type="checkbox" name="create_migration" data-toggle="toggle"
                       data-on="Yes, Please" data-off="No Thanks">
            </div>
        @endif
        </div><!-- .panel-body .row -->
        
        <div v-if="compositeIndexes.length" v-once class="alert alert-danger">
            <p>This table has composite indexes. Please note that they are not supported at the moment. Be careful when trying to add/remove indexes.</p>
        </div>

        <div id="alertsContainer"></div>

        <template v-if="tableHasColumns">
            <p>Table Columns</p>

            <table class="table table-bordered" style="width:100%;">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Length</th>
                    <th>Not Null</th>
                    <th>Unsigned</th>
                    <th>Auto Increment</th>
                    <th>Index</th>
                    <th>Default</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    <database-column
                        v-for="column in table.columns"
                        :column="column"
                        :index="getColumnsIndex(column.name)"
                        @columnNameUpdated="renameColumn"
                        @columnDeleted="deleteColumn"
                        @indexAdded="addIndex"
                        @indexDeleted="deleteIndex"
                        @indexUpdated="updateIndex"
                        @indexChanged="onIndexChange"
                    ></database-column>
                </tbody>
            </table>
        </template>
        <div v-else>
          <p>The table has no columns...</p>
        </div>

        <div style="text-align:center">
            <database-table-helper-buttons
                @columnAdded="addColumn"
            ></database-table-helper-buttons>
        </div>
    </div><!-- .panel-body -->

    <div class="panel-footer">
        <input type="submit" class="btn btn-primary pull-right"
               value="@if($db->action == 'update'){{ 'Update Table' }}@else{{ 'Create New Table' }}@endif"
               :disabled="!tableHasColumns">
        <div style="clear:both"></div>
    </div>
</div><!-- .panel -->


@endsection

@include('voyager::tools.database.vue-components.database-column')
@include('voyager::tools.database.vue-components.database-table-helper-buttons')

<script>
    Vue.component('database-table-editor', {
        props: {
            table: {
                type: Object,
                required: true
            }
        },
        data() {
            return {
                emptyIndex: {
                    type: '',
                    name: ''
                },
                compositeIndexes: []
            };
        },
        template: `@yield('database-table-editor-template')`,
        mounted() {
            // Add warning to columns that are part of a composite index
            this.compositeIndexes = this.getCompositeIndexes();
            let compositeColumns = this.getIndexesColumns(this.compositeIndexes);
            
            for (col in compositeColumns) {
                this.getColumn(compositeColumns[col]).composite = true;
            }

            // Display errors
            @if(Session::has('alerts'))
                displayAlerts(alerts, bootstrapAlerter({dismissible: true}), 'error');
            @endif
        },
        computed: {
            tableHasColumns() {
                return this.table.columns.length;
            }
        },
        methods: {
            addColumn(column) {
                column.name = column.name.trim();

                if (column.name && this.hasColumn(column.name)) {
                    return toastr.error("Column " + column.name + " already exists");
                }

                this.table.columns.push(
                    JSON.parse(JSON.stringify(column))
                );
            },
            getColumn(name) {
                name = name.toLowerCase().trim();

                return this.table.columns.find(function (column) {
                    return name == column.name.toLowerCase();
                });
            },
            hasColumn(name) {
                return !!this.getColumn(name);
            },
            renameColumn(column) {
                let newName = column.newName.trim();
                column = column.column;

                let existingColumn;
                if ((existingColumn = this.getColumn(newName)) && (existingColumn !== column)) {
                    return toastr.error("Column " + newName + " already exists");
                }

                let index = this.getColumnsIndex(column.name);
                if (index !== this.emptyIndex) {
                    index.columns = [newName];
                }

                column.name = newName;
            },
            deleteColumn(column) {
                var columnPos = this.table.columns.indexOf(column);
                
                if (columnPos !== -1) {
                    this.table.columns.splice(columnPos, 1);
                    
                    // Delete associated index
                    this.deleteIndex(this.getColumnsIndex(column.name));
                }
            },
            getColumnsIndex(columns) {
                // todo: detect if a column has a composite index
                //  if so, maybe disable its Index input, and tell the user to go to special Index form (advanced view)?
                if (!Array.isArray(columns)) {
                    columns = [columns];
                }

                let index = null;

                for (i in this.table.indexes) {
                    // if there is no difference between columns
                    if (!($(this.table.indexes[i].columns).not(columns).get().length)) {
                        index = this.table.indexes[i];
                        break;
                    }
                }

                if (!index) {
                    index = this.emptyIndex;
                }

                index.table = this.table.name;
                return index;
            },
            onIndexChange(index) {
                if (index.old === this.emptyIndex) {
                    return this.addIndex({
                        columns: index.columns,
                        type: index.newType
                    });
                }

                if (index.newType == '') {
                    return this.deleteIndex(index.old);
                }

                return this.updateIndex(index.old, index.newType);
            },
            addIndex(index) {
                if (index.type == 'PRIMARY') {
                    if (this.table.primaryKeyName) {
                        return toastr.error("The table already has a primary index.");
                    }

                    this.table.primaryKeyName = 'primary';
                }

                this.setIndexName(index);
                this.table.indexes.push(index);
            },
            deleteIndex(index) {
                var indexPos = this.table.indexes.indexOf(index);
                
                if (indexPos !== -1) {
                    if (index.type == 'PRIMARY') {
                        this.table.primaryKeyName = false;
                    }

                    this.table.indexes.splice(indexPos, 1);
                }
            },
            updateIndex(index, newType) {
                if (index.type == 'PRIMARY') {
                    this.table.primaryKeyName = false;
                } else if (newType == 'PRIMARY') {
                    if (this.table.primaryKeyName) {
                        return toastr.error("The table already has a primary index.");
                    }

                    this.table.primaryKeyName = 'primary';
                }

                index.type = newType;
                this.setIndexName(index);
            },
            setIndexName(index) {
                if (index.type == 'PRIMARY') {
                    index.name = 'primary';
                } else {
                    // the name will be set on the server by PHP
                    index.name = '';
                }
            },
            getCompositeIndexes() {
                let composite = [];

                for (i in this.table.indexes) {
                    if (this.table.indexes[i].isComposite) {
                        composite.push(this.table.indexes[i]);
                    }
                }

                return composite;
            },
            getIndexesColumns(indexes) {
                let columns = [];

                for (i in indexes) {
                    for (col in indexes[i].columns) {
                        columns.push(indexes[i].columns[col]);
                    }
                }

                return [...new Set(columns)];
            }
        }
    });
</script>
