<template>
    <div>
        <div class="flex mb-8 w-full">
            <div class="w-10/12">
                <a class="button green small" :href="route('voyager.'+translate(bread.slug, true)+'.create')">
                    {{ __('voyager::generic.add_type', {type: translate(bread.name_singular, true)}) }}
                </a>
                <bread-actions :actions="actions" :mass="true" :keys="selectedEntries" v-on:reset="selectedEntries = []" :bread="bread" />
                <a
                    class="button yellow small"
                    :href="route('voyager.bread.edit', bread.table)"
                    v-if="debug">
                    {{ __('voyager::bread.edit_name', {name: __('voyager::bread.bread')}) }}
                </a>
                <a
                    class="button yellow small"
                    :href="route('voyager.bread.edit', bread.table)+'?layout='+layoutId"
                    v-if="debug">
                    {{ __('voyager::bread.edit_name', {name: __('voyager::manager.layout')}) }}
                </a>
            </div>
            <div class="w-2/12 text-right">
                <input type="text" class="voyager-input small" :placeholder="__('voyager::generic.search')" v-model="parameter.globalSearch">
            </div>
        </div>
        
        <table v-bind:class="['voyager-table', loading ? 'opacity-25' : '']">
            <thead>
                <tr>
                    <th><input type="checkbox" @click="selectAll($event.target.checked)" ref="checkbox_all"></th>
                    <th v-for="(formfield, i) in layout.formfields" :key="'th-'+i" @click="formfield.options.sortable ? orderBy(formfield.field) : ''">
                        {{ translate(formfield.options.title, true) }}
                        <span v-if="formfield.options.sortable && parameter.orderField == formfield.field">
                            <span v-if="parameter.orderDir == 'asc'">
                                <!-- TODO: add ASC icon -->
                                &DoubleUpArrow;
                            </span>
                            <span v-else>
                                <!-- TODO: add DESC icon -->
                                &DoubleDownArrow;
                            </span>
                        </span>
                    </th>
                    <th class="text-right">{{ __('voyager::generic.actions') }}</th>
                </tr>
                <tr>
                    <th></th>
                    <th v-for="(formfield, i) in layout.formfields" :key="'th-search-'+i" @dblclick.prevent="clearFilter(formfield.field)">
                        <component
                            v-if="formfield.options.searchable"
                            v-model="parameter.filter[formfield.field]"
                            v-on:input="filter()"
                            :is="'formfield-'+formfield.type"
                            :options="formfield.options"
                            action="query">
                            <input type="text" class="voyager-input small"
                                v-model="parameter.filter[formfield.field]"
                                :placeholder="__('voyager::bread.search_field', { field: translate(formfield.options.title, true) })"
                                @input="filter()">
                        </component>
                    </th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(result, i) in results.rows" v-bind:key="'tr-'+i">
                    <td><input type="checkbox" v-model="selectedEntries" :value="result[results.primary]"></td>
                    <td v-for="(formfield, i) in layout.formfields" :key="'td-'+i">
                        <div v-if="isArray(getData(result, formfield.field))">
                            <div v-for="(relationship, key) in getData(result, formfield.field).slice(0, 3)" v-bind:key="key">
                                <component :is="formfield.options.link ? 'a' : 'div'" :href="getRelationshipLink(formfield, relationship)">
                                    <component
                                        :is="'formfield-'+formfield.type"
                                        :value="relationship[getRelationshipField(formfield.field)]"
                                        :options="formfield.options"
                                        action="browse" />
                                </component>
                            </div>
                            <i v-if="getData(result, formfield.field).length > 3">
                                {{ __('voyager::bread.results_more', {num: (getData(result, formfield.field).length - 3)}) }}
                            </i>
                        </div>
                        <div v-else>
                            <component :is="formfield.options.link ? 'a' : 'div'" :href="getLink(formfield, result)" :target="getTarget(formfield)">
                                <component
                                    :is="'formfield-'+formfield.type"
                                    :value="getData(result, formfield.field)"
                                    :options="formfield.options"
                                    action="browse" />
                            </component>
                        </div>
                    </td>
                    <td class="text-right">
                        <bread-actions :actions="result.actions" :mass="false" :keys="result[results.primary]" :bread="bread" :key="'actions-'+i" />
                    </td>
                </tr>
            </tbody>
        </table>
            
        <div v-bind:class="['flex mb-4 mt-4', loading ? 'opacity-25' : '']">
            <div class="w-1/2">
                <div v-if="results.rows && results.rows.length > 0">
                    {{ browseResultsDescription }}
                    <span v-if="results.filtered < results.records">
                        {{ __('voyager::bread.browse_filtered', { total: results.records, type: translate(bread.name_plural, true)}) }}
                        <a @click.prevent="clearFilter()" href="#">{{ __('voyager::bread.browse_filter_clear') }}</a>
                    </span>
                </div>
            </div>
            <div class="w-1/2">
                <div class="flex text-right">
                    <div class="w-full"></div>
                    <select class="voyager-input small w-auto mr-5" v-model.number="parameter.perPage" v-if="results.rows && results.rows.length > 0">
                        <option>10</option>
                        <option v-if="results.filtered >= 25">25</option>
                        <option v-if="results.filtered >= 50">50</option>
                        <option v-if="results.filtered >= 100">100</option>
                        <option :value="Number.MAX_SAFE_INTEGER">All</option>
                    </select>
                    <select class="voyager-input small w-auto mr-5" v-model="parameter.softDeletes" v-if="bread.soft_deletes == 'select'">
                        <option value="show">Show soft-deleted</option>
                        <option value="only">Show only soft-deleted</option>
                        <option value="hide">Hide soft-deleted</option>
                    </select>
                    <pagination :pages="pages" v-model.number="parameter.page" :visible-pages="7" v-if="results.rows && results.rows.length > 0" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['bread', 'accessors', 'layout', 'url', 'actions', 'translatable'],
    data: function () {
        return {
            results: [],
            loading: false,
            selectedEntries: [],
            parameter: {
                page: 1,
                perPage: 10,
                filter: {},
                globalSearch: '',
                orderField: '',
                orderDir: 'asc',
                softDeletes: 'hide',
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
            var filter = this.parameter.filter;
            for (var key in this.parameter.filter) {
                if (!filter.hasOwnProperty(key) || filter[key] === null || filter[key] == '') {
                    delete this.parameter.filter[key];
                }
            }
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
            vm.selectedEntries = [];
            vm.$refs['checkbox_all'].checked = false;
            axios.post(this.route('voyager.'+this.translate(this.bread.slug, true)+'.data'), vm.parameter)
            .then(function (response) {
                vm.results = response.data;
                vm.loading = false;

                if (history.pushState) {
                    var url = [];
                    for (var key in vm.parameter) {
                        if (key !== '_token' && vm.parameter.hasOwnProperty(key)) {
                            if (key == 'filter') {
                                url.push(key + '=' + encodeURIComponent(JSON.stringify(vm.parameter[key])));
                            } else if (key !== 'softDeletes' || vm.bread.soft_deletes == 'select') {
                                url.push(key + '=' + encodeURIComponent(vm.parameter[key]));
                            }
                        }
                    }
                    url = url.join('&');
                    url = vm.url + '?' + url;
                    window.history.pushState({ path:  vm.url+'?'+url }, '', url);
                }
            })
            .catch(function (error) {
                vm.$snotify.error(error);
                if (vm.debug) {
                    vm.debug(error.response.data.message, true, 'error');
                }
                vm.loading = false;
            });
        }, 250),
        getData: function (result, field) {
            var snake_field = snake_case(field);
            if (field.includes('.')) {
                var parts = snake_field.split('.');
                var results = result[parts[0]];
                if (window.isArray(results)) {
                    if (this.isFieldTranslatable(field)) {
                        var trans_results = JSON.parse(JSON.stringify(results));
                        var vm = this;
                        results.forEach(function (result, key) {
                            trans_results[key][parts[1]] = vm.translate(result[parts[1]]);
                        });

                        return trans_results;
                    }

                    return results;
                } else if (isObject(results)) {
                    if (this.isFieldTranslatable(field)) {
                        return this.translate(results[parts[1]]);
                    } else {
                        return results[parts[1]];
                    }
                } else {
                    return 'Nothing';
                }
            }

            if (this.isFieldTranslatable(field)) {
                return this.translate(result[field]);
            }

            return result[field] || '';
        },
        isFieldTranslatable: function (field) {
            return this.translatable.includes(field);
        },
        isArray: function (input) {
            return window.isArray(input);
        },
        getRelationshipField: function (field) {
            return field.substr(field.indexOf('.') + 1);
        },
        getLink: function (formfield, result) {
            if (!formfield.options.link || false) {
                return false;
            }
            var key = result[this.results.primary];

            var action = 'show';
            if (typeof formfield.options.link === 'string' && formfield.options.link.includes('edit')) {
                action = 'edit';
            }

            return Vue.prototype.route('voyager.'+this.translate(this.bread.slug, true)+'.'+action, key);
        },
        getTarget: function (formfield) {
            if (!formfield.options.link || false) {
                return false;
            }
            if (typeof formfield.options.link === 'string' && formfield.options.link.includes('_new')) {
                return '_blank';
            }

            return false;
        },
        getRelationshipLink: function (formfield, result) {
            if (!formfield.options.link || false) {
                return false;
            }

            //return route();
            //console.log(result);

            return '#';
        },
        clearFilter: function (field = null) {
            if (field) {
                if (!this.parameter.filter[field]) {
                    return;
                }
                this.parameter.filter[field] = null;
            } else {
                this.parameter.filter = {};
                this.parameter.globalSearch = '';
            }
            this.filter();
        },
    },
    computed: {
        pages: function () {
            var pages = Math.ceil(this.results.filtered / this.parameter.perPage);
            pages < 1 ? pages = 1 : pages = pages;

            return pages;
        },
        browseResultsDescription: function () {
            var description = '';
            var start = (this.parameter.page - 1) * this.parameter.perPage + 1;
            var end = start + parseInt(this.parameter.perPage) - 1;
            var total = this.results.filtered;
            if (end > total) {
                end = total;
            }
            if (this.results.rows.length == 1) {
                description = this.__('voyager::bread.browse_results', {
                    start: start,
                    end: end,
                    total: total,
                    type: this.translate(this.bread.name_singular, true)
                });
            } else {
                description = this.__('voyager::bread.browse_results', {
                    start: start,
                    end: end,
                    total: total,
                    type: this.translate(this.bread.name_plural, true)
                });
            }

            return description;
        },
        layoutId: function () {
            var vm = this;
            var id = 0;
            vm.bread.layouts.forEach(function (layout, key) {
                if (layout.name == vm.layout.name) {
                    id = key;
                }
            });

            return id;
        }
    },
    watch: {
        'parameter.page': function () {
            this.loadItems();
        },
        'parameter.perPage': function () {
            this.loadItems();
        },
        'parameter.softDeletes': function () {
            this.loadItems();
        },
        'parameter.globalSearch': function (value) {
            this.loadItems();
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
            this.parameter = Object.assign(this.parameter, params);
        }

        if (this.parameter.orderField == '') {
            this.orderBy(this.layout.default_sort_field || '');
        }

        if (this.translatable.length > 0) {
            Vue.prototype.$language.localePicker = true;
        }
    }
};
</script>