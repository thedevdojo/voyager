@section('database-column-template')

<tr class="newTableRow">
    <td>
        <input v-model.trim="column.name" @input="onColumnNameInput" type="text" class="form-control" required pattern="{{ $table->identifierRegex }}">
    </td>

    <td>
        <select :value="column.type" @change="onColumnTypeChange" class="form-control" tabindex="-1">
        @foreach($table->columnTypes as $category => $types)
            <optgroup label="{{ $category }}">
            @foreach($types as $type)
                <option value="{{ $type }}">{{ strtoupper($type) }}</option>
            @endforeach
            </optgroup>
        @endforeach
        </select>
        <div v-if="column.type=='enum'">
            <small>Enter Values (comma separated)</small>
            <input type="text" class="form-control" name="enum[]">
        </div>
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
        <select :value="index.type" @change="onIndexTypeChange" :disabled="indexNotSupported" class="form-control" tabindex="-1">
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
                var newName = event.target.value;

                // update corresponding index
                this.index.columns = [newName];
            },
            onColumnTypeChange(event) {
                var type = event.target.value;

                if (!this.columnTypeSupportsIndex(type) && this.index.type) {
                    return toastr.error("Type " + type + " doesn't support indexes");
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
            },
            columnTypeSupportsIndex(columnType) {
                switch (columnType) {
                    case 'text':
                    case 'blob':
                    case 'simple_array':
                        return false;

                    default:
                        return true;
                }
            }
        },
        computed: {
            indexNotSupported() {
                return !this.columnTypeSupportsIndex(this.column.type);
            }
        }
    });
</script>
