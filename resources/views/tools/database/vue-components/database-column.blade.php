@section('database-column-template')

<tr class="newTableRow">
    <td>
        <input :value="column.name" @input="onColumnNameInput" type="text" class="form-control" required pattern="{{ $db->identifierRegex }}">
    </td>

    <td>
        <database-types
            :column="column"
            @typeChanged="onColumnTypeChange">
        </database-types>
    </td>

    <td>
        <input v-model.number="column.length" type="number" min="0">
    </td>

    <td>
        <input v-model="column.notnull" type="checkbox">
    </td>

    <td>
        <input v-model="column.unsigned" type="checkbox">
    </td>

    <td>
        <input v-model="column.autoincrement" type="checkbox">
    </td>

    <td>
        <select :value="index.type" @change="onIndexTypeChange"
                :disabled="column.type.notSupportIndex"
                class="form-control" tabindex="-1">
            <option value=""></option>
            <option value="INDEX">{{ trans('voyager.database_index') }}</option>
            <option value="UNIQUE">{{ trans('voyager.database_unique') }}</option>
            <option value="PRIMARY">{{ trans('voyager.database_primary') }}</option>
        </select>
        <small v-if="column.composite" v-once>{{ trans('voyager.database_composite_warning') }}</small>
    </td>

    <td>
        <database-column-default :column="column"></database-column-default>
    </td>

    <td>
        <div class="btn btn-danger delete-row" @click="deleteColumn"><i class="voyager-trash"></i></div>
    </td>

</tr>

@endsection

@include('voyager::tools.database.vue-components.database-types')
@include('voyager::tools.database.vue-components.database-column-default')

<script>
    Vue.component('database-column', {
        props: {
            column: {
                type: Object,
                required: true
            },
            index: {
                type: Object,
                required: true
            }
        },
        template: `@yield('database-column-template')`,
        methods: {
            deleteColumn() {
                this.$emit('columnDeleted', this.column);

                // todo: add an UNDO button or something in case the user mistakenly deletes the column
            },
            onColumnNameInput(event) {
                let newName = event.target.value;

                this.$emit('columnNameUpdated', {
                    column: this.column,
                    newName: newName
                });
            },
            onColumnTypeChange(type) {
                if (type.notSupportIndex && this.index.type) {
                    this.$emit('indexDeleted', this.index);
                }

                // Reset default value
                this.column.default = null;

                this.column.type = type;
            },
            onIndexTypeChange(event) {
                if (this.column.name == '') {
                    return toastr.error("{{ trans('voyager.database_name_warning') }}");
                }

                return this.$emit('indexChanged', {
                    columns: [this.column.name],
                    old: this.index,
                    newType: event.target.value
                });
            }
        }
    });
</script>
