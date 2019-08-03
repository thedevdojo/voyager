<template>
    <div>
        <table class="w-full">
            <thead>
                <tr>
                    <th><input type="checkbox" @click="selectAll($event.target.checked)"></th>
                    <th v-for="(formfield, i) in layout.formfields" :key="'th-'+i" @click="orderBy(formfield.options.field)">
                        {{ translate(formfield.options.title) }}
                    </th>
                    <th>{{ __('voyager::generic.actions') }}</th>
                </tr>
                <tr>
                    <th></th>
                    <th v-for="(formfield, i) in layout.formfields" :key="'th-search-'+i">
                        <input type="text" class="form-control"
                            v-if="formfield.options.searchable"
                            v-model="parameter.filter[formfield.options.field]"
                            :placeholder="__('voyager::bread.search_field', { field: translate(formfield.options.title) })"
                            @input="filter()">
                    </th>
                    <th>
                        
                    </th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    props: ['bread', 'layout', 'data-url'],
    data: function () {
        return {
            results: [],
            loading: false,
            parameter: {
                page: 1,
                perPage: 10,
                filter: {},
                orderField: '',
                orderDir: 'asc',
                _token: '',
            }
        };
    },
    methods: {
        orderBy: function (field) {
            if (this.parameter.orderField == field) {
                if (this.parameter.orderDir == 'asc') {
                    this.parameter.orderDir = 'desc';
                } else {
                    this.parameter.orderDir = 'asc';
                }
            } else {
                this.parameter.orderField = field;
                this.parameter.orderDir = 'asc';
            }
            this.loadItems();
        },
        filter: function () {
            this.parameter.page = 1;
            this.loadItems();
        },
        selectAll: function (select) {
            
        },
        loadItems: function () {
            var vm = this;
            vm.loading = true;
            axios.get(this.dataUrl, vm.parameter)
            .then(function (response) {
                vm.results = response.data;
                vm.loading = false;
            })
            .catch(function (error) {
                vm.$snotify.error(error);
                vm.loading = false;
            });
        }
    },

    mounted: function () {
        this.loadItems();
    }
};
</script>