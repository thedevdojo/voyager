@section('database-types-template')

<div>
    <select :value="column.type.name" @change="onTypeChange" class="form-control" tabindex="-1" required>
        <optgroup v-for="(types, category) in dbTypes" :label="category">
            <option v-for="type in types" :value="type.name" :disabled="type.notSupported">
                @{{ type.name.toUpperCase() }}
            </option>
        </optgroup>
    </select>
    <div v-if="column.type.notSupported">
        <small>This type is not supported</small>
    </div>
</div>


@endsection

<script>
    let databaseTypes = {!! json_encode($db->types) !!};

    Vue.component('database-types', {
        props: {
            column: {
                type: Object,
                required: true
            }
        },
        data() {
            return {
                dbTypes: databaseTypes
            };
        },
        template: `@yield('database-types-template')`,
        methods: {
            onTypeChange(event) {
                this.$emit('typeChanged', this.getType(event.target.value));
            },
            getType(name) {
                let type;

                for (category in this.dbTypes) {
                    type = this.dbTypes[category].find(function(type) {
                        return name == type.name;
                    });

                    if (type) {
                        return type;
                    }
                }
            }
        }
    });
</script>
