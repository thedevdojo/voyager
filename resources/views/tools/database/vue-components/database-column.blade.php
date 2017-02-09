@section('database-column-template')

<tr class="newTableRow">
    <td>
        <input v-model.trim="column.name" @input="onColumnNameInput" type="text" class="form-control" required pattern="{{ $database->identifierRegex }}">
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
            <option value="INDEX">INDEX</option>
            <option value="UNIQUE">UNIQUE</option>
            <option value="PRIMARY">PRIMARY</option>
        </select>
    </td>

    <td>
        <input v-model.trim="column.default" type="text" class="form-control">
    </td>

    <td>
        <div class="btn btn-danger delete-row" @click="deleteColumn"><i class="voyager-trash"></i></div>
    </td>

</tr>

@endsection

@include('voyager::tools.database.vue-components.database-types')

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

                // update corresponding index
                this.index.columns = [newName];
            },
            onColumnTypeChange(type) {
                if (type.notSupportIndex && this.index.type) {
                    this.$emit('indexDeleted', this.index);
                }

                this.column.type = type;
            },
            onIndexTypeChange(event) {
                var newType = event.target.value;

                // if newType is empty, it means the user wants to delete the index
                if (newType == '') {
                    return this.$emit('indexDeleted', this.index);
                }

                var index = {
                    index: this.index,
                    newType: newType
                };

                // if the current type is empty, it means the user wants to add a new index
                if (this.index.type == '') {
                    return this.$emit('indexAdded', index);
                }

                // just update the type
                this.$emit('indexUpdated', index);
            }
        }
    });
</script>
