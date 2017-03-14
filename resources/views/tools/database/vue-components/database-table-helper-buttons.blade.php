@section('database-table-helper-buttons-template')
    <div>
        <div class="btn btn-success" @click="addNewColumn">+ Add New Column</div>
        <div class="btn btn-success" @click="addTimestamps">+ Add Timestamps</div>
        <div class="btn btn-success" @click="addSoftDeletes">+ Add Soft Deletes</div>
    </div>
@endsection

<script>
    Vue.component('database-table-helper-buttons', {
        template: `@yield('database-table-helper-buttons-template')`,
        methods: {
            addColumn(column) {
                this.$emit('columnAdded', column);
            },
            makeColumn(options) {
                return $.extend({
                    name: '',
                    oldName: '',
                    type: databaseTypes.getType('integer'),
                    length: null,
                    fixed: false,
                    unsigned: false,
                    autoincrement: false,
                    notnull: false,
                    default: null
                }, options);
            },
            addNewColumn() {
                this.addColumn(this.makeColumn());
            },
            addTimestamps() {
                this.addColumn(this.makeColumn({
                    name: 'created_at',
                    type: databaseTypes.getType('timestamp')
                }));

                this.addColumn(this.makeColumn({
                    name: 'updated_at',
                    type: databaseTypes.getType('timestamp')
                }));
            },
            addSoftDeletes() {
                this.addColumn(this.makeColumn({
                    name: 'deleted_at',
                    type: databaseTypes.getType('timestamp')
                }));
            }
        }
    });
</script>
