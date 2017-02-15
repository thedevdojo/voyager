@section('database-types-template')

<div>
    <select :value="column.type.name" @change="onTypeChange" class="form-control" tabindex="-1">
        <option v-for="type in types" :value="type.name">
                @{{ type.name.toUpperCase() }}
        </option>
    </select>
    <div v-if="column.type.notSupported">
        <small>Warning: This type is not supported</small>
    </div>
</div>


@endsection

<script>
    let databaseTypes = {!! json_encode($database->types) !!};

    Vue.component('database-types', {
        props: {
            column: {
                type: Object,
                required: true
            }
        },
        data() {
            return {
                types: databaseTypes
            };
        },
        template: `@yield('database-types-template')`,
        methods: {
            onTypeChange(event) {
                this.$emit('typeChanged', this.getType(event.target.value));
            },
            getType(name) {
                return this.types.find(function(type) {
                    return name == type.name;
                });
            }
        }
    });
</script>
