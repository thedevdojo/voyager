<template>
    <div>
        <locale-picker v-if="translatable" />
        <br>
        <bread-actions :actions="actions" :mass="true" :keys="selectedEntries" :bread="bread" />
        <table class="voyager-table">
            <thead>
                <tr>
                    <th><input type="checkbox" @click="selectAll($event.target.checked)"></th>
                    <th v-for="(formfield, i) in layout.formfields" :key="'th-'+i" @click="formfield.options.sortable ? orderBy(formfield.options.field) : ''">
                        {{ translate(formfield.options.title, true) }}
                        <div v-if="formfield.options.sortable && parameter.orderField == formfield.options.field">
                            <div v-if="parameter.orderDir == 'asc'">
                                <!-- TODO: add ASC icon -->
                            </div>
                            <div v-else>
                                <!-- TODO: add DESC icon -->
                            </div>
                        </div>
                    </th>
                    <th>{{ __('voyager::generic.actions') }}</th>
                </tr>
                <tr>
                    <th></th>
                    <th v-for="(formfield, i) in layout.formfields" :key="'th-search-'+i">
                        <input type="text" class="voyager-input small"
                            v-if="formfield.options.searchable"
                            v-model="parameter.filter[formfield.options.field]"
                            :placeholder="__('voyager::bread.search_field', { field: translate(formfield.options.title, true) })"
                            @input="filter()">
                    </th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(result, i) in results.rows" v-bind:key="'tr-'+i">
                    <td><input type="checkbox" v-model="selectedEntries" :value="result[results.primary]"></td>
                    <td v-for="(formfield, i) in layout.formfields" :key="'td-'+i">
                        <div v-if="isArray(getData(result, formfield.options.field))">
                            <div v-for="(relationship, key) in getData(result, formfield.options.field).slice(0, 3)" v-bind:key="key">
                                <component
                                    :is="'formfield-'+formfield.type"
                                    :data="relationship[getRelationshipField(formfield.options.field)]"
                                    :options="formfield.options"
                                    action="browse" />
                            </div>
                            <div v-if="getData(result, formfield.options.field).length > 3">
                                {{ __('voyager::bread.results_more', {num: (getData(result, formfield.options.field).length - 3)}) }}
                            </div>
                        </div>
                        <div v-else>
                            <component
                                :is="'formfield-'+formfield.type"
                                :data="getData(result, formfield.options.field)"
                                :options="formfield.options"
                                action="browse" />
                        </div>
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
    props: ['bread', 'accessors', 'layout', 'data-url', 'actions', 'translatable'],
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
            if (!this.isFieldOrderable(field)) {
                return;
            }
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
        isFieldOrderable: function (field) {
            return !this.accessors.includes(field);
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
        }, 200),
        getData: function (result, field, max = null) {
            if (field.includes('.')) {
                var parts = field.split('.');
                var results = result[parts[0]];
                if (max) {
                    results.length = Vue.prototype.clamp(max, 0, results.length);
                }
                if (this.isFieldTranslatable(field)) {
                    // TODO: translate 
                }

                return results;
            }

            if (this.isFieldTranslatable(field)) {
                result[field] = Vue.prototype.get_input_as_translatable_object(result[field]);

                return Vue.prototype.translate(result[field]);
            }
        },
        isFieldTranslatable: function (field) {
            return this.translatable.includes(field);
        },
        getRelationshipField: function (field) {
            return field.substr(field.indexOf('.') + 1);
        }
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

        if (this.parameter.orderField == '') {
            this.orderBy(this.layout.default_sort_field || '');
        }

        this.loadItems();
    }
};
</script>