<template>
    <div :class="fromRelationship ? 'mode-dark' : ''">
        <div class="flex mb-8 w-full" v-if="!fromRelationship">
            <div class="w-10/12">
                <a class="button green small" :href="route('voyager.'+translate(bread.slug, true)+'.add')">
                    {{ __('voyager::generic.add_type', {type: translate(bread.name_singular, true)}) }}
                </a>
                <bread-actions :actions="actions" :mass="true" :keys="selectedEntries" v-on:reset="selectedEntries = []" :bread="bread" />
                <a
                    class="button yellow small"
                    :href="route('voyager.bread.edit', bread.table)"
                    v-if="debug">
                    {{ __('voyager::generic.edit_type', {type: __('voyager::bread.bread')}) }}
                </a>
                <a
                    class="button yellow small"
                    :href="route('voyager.bread.edit', bread.table)+'?layout='+layoutId"
                    v-if="debug">
                    {{ __('voyager::generic.edit_type', {type: __('voyager::manager.layout')}) }}
                </a>
            </div>
            <div class="w-2/12 text-right">
                <input type="text" class="voyager-input small" :placeholder="__('voyager::generic.search')" v-model="parameter.globalSearch">
            </div>
        </div>
        <div class="voyager-table striped">
            <table v-bind:class="[loading ? 'loading' : '']">
                <thead>
                    <tr>
                        <th><input type="checkbox" :disabled="!multiple" @click="selectAll($event.target.checked)" ref="checkbox_all"></th>
                        <th
                            v-for="(formfield, i) in layout.formfields"
                            :key="'th-'+i"
                            @click="formfield.options.sortable ? orderBy(formfield.column) : ''"
                            :class="formfield.options.sortable ? 'cursor-pointer' : ''">
                            {{ translate(formfield.options.title, true) }}
                            <span v-if="formfield.options.sortable && parameter.orderColumn == formfield.column" class="text-gray-800 dark:text-gray-200">
                                <i class="uil text-xl" :class="[parameter.orderDir == 'asc' ? 'uil-sort-amount-up' : 'uil-sort-amount-down']"></i>
                            </span>
                        </th>
                        <th class="text-right" v-if="!fromRelationship">{{ __('voyager::generic.actions') }}</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th v-for="(formfield, i) in layout.formfields" :key="'th-search-'+i" @dblclick.prevent="clearFilter(formfield.column)">
                            <component
                                class="my-3 mr-2"
                                v-if="formfield.options.searchable"
                                v-bind:value="parameter.filter[formfield.column]"
                                v-on:input="filterBy($event, formfield.column)"
                                :is="'formfield-'+formfield.type"
                                :options="formfield.options"
                                :placeholder="__('voyager::bread.search_column', { column: translate(formfield.options.title, true) })"
                                action="query">
                                <input type="text" class="voyager-input small"
                                    v-bind:value="parameter.filter[formfield.column]"
                                    :placeholder="__('voyager::bread.search_column', { column: translate(formfield.options.title, true) })"
                                    @input="filterBy($event.target.value, formfield.column)">
                            </component>
                        </th>
                        <th v-if="!fromRelationship"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(result, i) in results.rows"
                        v-bind:key="'tr-'+i"
                        @dblclick.stop="$emit('select-relationship', result[results.primary])">
                        <td>
                            <input
                                :type="multiple ? 'checkbox' : 'radio'"
                                v-model="selectedEntries"
                                :value="result[results.primary]"
                                @click="select($event, result[results.primary])">
                        </td>
                        <td v-for="(formfield, i) in layout.formfields" :key="'td-'+i">
                            <div v-if="isArray(getData(result, formfield))">
                                <div v-for="(relationship, key) in getData(result, formfield).slice(0, 3)" v-bind:key="key">
                                    <component :is="formfield.options.link ? 'a' : 'div'" :href="getRelationshipLink(formfield, relationship)">
                                        <component
                                            :is="'formfield-'+formfield.type"
                                            :value="relationship"
                                            :options="formfield.options"
                                            action="browse" />
                                    </component>
                                </div>
                                <i v-if="getData(result, formfield).length > 3">
                                    {{ __('voyager::bread.results_more', {num: (getData(result, formfield).length - 3)}) }}
                                </i>
                            </div>
                            <div v-else>
                                <component :is="formfield.options.link ? 'a' : 'div'" :href="getLink(formfield, result)" :target="getTarget(formfield)">
                                    <component
                                        :is="'formfield-'+formfield.type"
                                        :value="getData(result, formfield)"
                                        :options="formfield.options"
                                        action="browse" />
                                </component>
                            </div>
                        </td>
                        <td class="text-right" v-if="!fromRelationship">
                            <bread-actions :actions="result.actions" :mass="false" :keys="result[results.primary]" :bread="bread" :key="'actions-'+i" />
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- TODO: Add pagination here? -->
        </div>
            
        <div v-bind:class="['flex mb-4 mt-4', loading ? 'opacity-25' : '']">
            <div class="w-1/2">
                <div v-if="results.rows">
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
                    <select class="voyager-input small w-auto mr-5" v-model="parameter.softDeletes" v-if="layout.soft_deletes == 'select'">
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
    props: {
        'bread': {
            type: Object,
        },
        'accessors': {
            type: Array,
        },
        'layout': {
            type: Object,
        },
        'actions': {
            type: Object,
        },
        'from-relationship': {
            type: Boolean,
            default: false,
        },
        'foreign-key': {
            required: false,
        },
        // Only used when coming from a relationship
        'selected': {
            type: [Array, Number],
            required: false,
        },
        'multiple': {
            type: Boolean,
            default: true,
        }
    },
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
                orderColumn: '',
                orderDir: 'asc',
                softDeletes: 'hide',
            },
            debounced: debounce(this.loadItems, 250),
        };
    },
    methods: {
        orderBy: function (column) {
            if (!this.isColumnOrderable(column)) {
                return;
            }
            if (this.parameter.orderColumn == column) {
                if (this.parameter.orderDir == 'asc') {
                    this.parameter.orderDir = 'desc';
                } else {
                    this.parameter.orderDir = 'asc';
                }
            } else {
                this.parameter.orderColumn = column;
                this.parameter.orderDir = 'asc';
            }
            this.debounced();
        },
        isColumnOrderable: function (column) {
            return !this.accessors.includes(column);
        },
        filterBy: function (filter, column) {
            this.parameter.filter[column] = filter;

            this.filter();
        },
        filter: function () {
            var filter = this.parameter.filter;
            for (var key in this.parameter.filter) {
                if (!filter.hasOwnProperty(key) || filter[key] === null || filter[key] == '') {
                    delete this.parameter.filter[key];
                }
            }
            this.parameter.page = 1;
            this.debounced();
        },
        selectAll: function (select) {
            var vm = this;
            var pivot_selected = vm.selectedEntries;
            vm.selectedEntries = [];
            if (select) {
                vm.results.rows.forEach(function (row) {
                    vm.selectedEntries.push(row[vm.results.primary]);
                    vm.$emit('select', {
                        key: row[vm.results.primary],
                        selected: true
                    });
                });
            } else {
                pivot_selected.forEach(function (row) {
                    vm.$emit('select', {
                        key: row,
                        selected: false
                    });
                });
            }
        },
        select: function (e, key) {
            this.$emit('select', {
                key: key,
                selected: e.target.checked
            });
        },
        loadItems: function () {
            var vm = this;
            vm.loading = true;
            vm.selectedEntries = [];
            vm.$refs['checkbox_all'].checked = false;
            axios.post(this.route('voyager.'+this.translate(this.bread.slug, true)+'.data'), vm.parameter)
            .then(function (response) {
                vm.results = response.data;
                if (history.pushState && !vm.fromRelationship) {
                    var url = document.location.href;
                    for (var key in vm.parameter) {
                        if (vm.parameter.hasOwnProperty(key)) {
                            if (key == 'filter') {
                                url = vm.addParameterToUrl(key, JSON.stringify(vm.parameter[key]), url);
                            } else if (key !== 'softDeletes' || vm.layout.soft_deletes == 'select') {
                                url = vm.addParameterToUrl(key, vm.parameter[key], url);
                            }
                        }
                    }
                    vm.pushToUrlHistory(url);
                }
            })
            .catch(function (error) {
                vm.$snotify.error(error);
                if (vm.debug) {
                    vm.debug(error.response.data.message, true, 'error');
                }
            }).finally(function () {
                vm.loading = false;
            });
        },
        getData: function (result, formfield) {
            if (formfield.options.translatable || false) {
                return this.translate(result[formfield.column]);
            }

            return result[formfield.column] || '';
        },
        isArray: function (input) {
            return window.isArray(input);
        },
        getRelationshipColumn: function (column) {
            return column.substr(column.indexOf('.') + 1);
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

            // TODO: ...

            return '#';
        },
        clearFilter: function (column = null) {
            if (column) {
                if (!this.parameter.filter[column]) {
                    return;
                }
                this.parameter.filter[column] = null;
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
            if (total == 0) {
                start = 0;
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
            this.debounced();
        },
        'parameter.perPage': function () {
            this.debounced();
        },
        'parameter.softDeletes': function () {
            this.debounced();
        },
        'parameter.globalSearch': function (value) {
            this.debounced();
        },
        'selected': function (selected) {
            this.selectedEntries = selected;
        },
    },
    mounted: function () {
        var search = location.search.substring(1);
        if (search !== '') {
            for(var param of this.getParametersFromUrl()) {
                if (param[0] == 'filter') {
                    param[1] = JSON.parse(decodeURIComponent(param[1]));
                }
                Vue.set(this.parameter, param[0], param[1]);
            }
        }

        if (this.parameter.orderColumn == '') {
            this.orderBy(this.layout.default_sort_column || '');
        }

        // TODO: Hide locale-picker if there are no translatable formfields
        Vue.prototype.$language.localePicker = true;
    }
};
</script>