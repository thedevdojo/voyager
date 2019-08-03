<template>
    <div>
        <bread-actions :actions="actions" :mass="true" :keys="selectedEntries" :bread="bread" />
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
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(result, i) in results.rows" v-bind:key="'tr-'+i">
                    <td><input type="checkbox" v-model="selectedEntries" :value="result[results.primary]"></td>
                    <td v-for="(formfield, i) in layout.formfields" :key="'td-'+i">
                        {{ result[formfield.options.field] || 'hh' }}
                    </td>
                    <td>
                        <bread-actions :actions="actions" :mass="false" :keys="result[results.primary]" :bread="bread" />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    props: ['bread', 'layout', 'data-url', 'actions'],
    data: function () {
        return {
            results: [],
            loading: false,
            selectedEntries: [],
            parameter: {
                page: 1,
                perPage: 10,
                filter: {},
                orderField: '',
                orderDir: 'asc',
                _token: document.head.querySelector('meta[name="csrf-token"]').content
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
            var vm = this;
            vm.selectedEntries = [];
            if (select) {
                vm.results.rows.forEach(function (row) {
                    vm.selectedEntries.push(row[vm.results.primary]);
                });
            }
        },
        loadItems: debounce(function () {
            var vm = this;
            vm.loading = true;
            axios.get(this.dataUrl, {
                params: vm.parameter
            })
            .then(function (response) {
                vm.results = response.data;
                vm.loading = false;

                if (history.pushState) {
                    var url = response.request.responseURL;
                    url = url.substring(0, url.indexOf('_token') - 1);
                    window.history.pushState({ path:  url }, '', url);
                }
            })
            .catch(function (error) {
                vm.$snotify.error(error);
                vm.loading = false;
            });
        }, 200)
    },

    mounted: function () {
        var search = location.search.substring(1);
        if (search !== '') {
            var params = JSON.parse('{"' + search.replace(/&/g, '","').replace(/=/g,'":"') + '"}', function(key, value) {
                if (key == 'filter') {
                    return JSON.parse(decodeURIComponent(value));
                }
                return key === "" ? value : decodeURIComponent(value);
            });
            params['_token'] = document.head.querySelector('meta[name="csrf-token"]').content;
            Vue.set(this, 'parameter', params);
        }

        this.loadItems();
    }
};
</script>