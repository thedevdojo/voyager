<template>
    <card :title="__('voyager::bread.browse_type', { type: translate(bread.name_plural) })" :icon="bread.icon">
        <div slot="actions">
            <div class="flex items-center">
                <input
                    type="text"
                    class="voyager-input w-full small ltr:mr-2 rtl:ml-2"
                    v-model="parameters.query"
                    @dblclick="parameters.query = null"
                    :placeholder="'Search ' + translate(bread.name_plural)">
                <button class="button blue m-0" @click.stop="load">
                    <icon icon="sync" :class="[loading ? 'rotating-ccw' : '']"></icon>
                    <span>{{ __('voyager::generic.reload') }}</span>
                </button>
                <button class="button red m-0 ml-2" v-if="deletableEntries > 0" @click.prevent="deleteEntries(selected)">
                    <icon icon="trash"></icon>
                    <span>{{ trans_choice('voyager::bread.delete_type', deletableEntries, { num: deletableEntries, types: translate(bread.name_plural, true), type: translate(bread.name_singular, true)}) }}</span>
                </button>
                <button class="button green m-0 ml-2" v-if="restorableEntries > 0" @click.prevent="restoreEntries(selected)">
                    <icon icon="history"></icon>
                    <span>{{ trans_choice('voyager::bread.restore_type', restorableEntries, { num: restorableEntries, types: translate(bread.name_plural, true), type: translate(bread.name_singular, true) }) }}</span>
                </button>
            </div>
        </div>
        <div>
            <div v-if="layout !== null">
                <div class="voyager-table striped" :class="[loading ? 'loading' : '']">
                    <table>
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" class="voyager-input" @change="selectAll($event.target.checked)" :checked="allSelected" />
                                </th>
                                <th
                                    v-for="(formfield, key) in layout.formfields" :key="'thead-' + key"
                                    :class="formfield.orderable ? 'cursor-pointer' : ''"
                                    @click="formfield.orderable ? orderBy(formfield.column.column) : ''">
                                    <div class="flex h-full items-center">
                                        {{ translate(formfield.title, true) }}
                                        <icon
                                            v-if="formfield.orderable && parameters.order == formfield.column.column"
                                            :icon="parameters.direction == 'asc' ? 'sort-amount-up' : 'sort-amount-down'"
                                            :size="5" class="ltr:ml-2 rtl:mr-2"
                                        ></icon>
                                    </div>
                                </th>
                                <th class="ltr:text-right rtl:text-left">
                                    {{ __('voyager::generic.actions') }}
                                </th>
                            </tr>
                            <tr>
                                <th></th>
                                <th v-for="(formfield, key) in layout.formfields" :key="'thead-search-' + key">
                                    <component
                                        v-if="formfield.searchable"
                                        v-model="parameters.filters[formfield.column.column]"
                                        :is="'formfield-'+formfield.type+'-browse'"
                                        :options="formfield.options"
                                        show="query">
                                        <input type="text" class="voyager-input small w-full"
                                            :placeholder="'Search ' + translate(formfield.title, true)"
                                            @dblclick="parameters.filters[formfield.column.column] = ''"
                                            v-model="parameters.filters[formfield.column.column]">
                                    </component>
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(result, key) in results" :key="'row-' + key">
                                <td>
                                    <input
                                        type="checkbox"
                                        class="voyager-input"
                                        v-model="selected"
                                        :value="result[primary]" />
                                </td>
                                <td v-for="(formfield, key) in layout.formfields" :key="'row-' + key">
                                    <component
                                        :is="'formfield-'+formfield.type+'-browse'"
                                        :options="formfield.options"
                                        :value="result[formfield.column.column]">
                                    </component>
                                </td>
                                <td class="ltr:text-right rtl:text-left">
                                    <a :href="route('voyager.'+translate(bread.slug, true)+'.read', result[primary])" class="button blue small">
                                        <icon icon="book-alt"></icon>
                                        <span>Read</span>
                                    </a>
                                    <a :href="route('voyager.'+translate(bread.slug, true)+'.edit', result[primary])" class="button yellow small">
                                        <icon icon="pen"></icon>
                                        <span>Edit</span>
                                    </a>
                                    <button @click.prevent="deleteEntries(result[primary])" class="button red small" v-if="(result.uses_soft_deletes && !result.is_soft_deleted) || !result.uses_soft_deletes">
                                        <icon icon="trash"></icon>
                                        <span>Delete</span>
                                    </button>
                                    <button @click.prevent="deleteEntries(result[primary], true)" v-if="result.uses_soft_deletes && result.is_soft_deleted" class="button red small">
                                        <icon icon="trash"></icon>
                                        <span>Force Delete</span>
                                    </button>
                                    <button @click.prevent="restoreEntries(result[primary])" v-if="result.uses_soft_deletes && result.is_soft_deleted" class="button green small">
                                        <icon icon="history"></icon>
                                        <span>Restore</span>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="results.length == 0 && !loading">
                                <td :colspan="layout.formfields.length + 2" class="text-center">
                                    <h4>{{ __('voyager::bread.no_results') }}</h4>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex w-full">
                    <div class="hidden lg:block w-1/2 whitespace-no-wrap">
                        {{ resultDescription }}
                        <a href="#" @click.prevent="parameters.filters = {}; parameters.query = null" v-if="showClearFilterButton">
                            {{ __('voyager::bread.clear_all_filters') }}
                        </a>
                    </div>
                    <div class="w-full lg:w1/2 lg:text-right">
                        <pagination :pages="pages" v-model.number="parameters.page" :visible-pages="7" v-if="results.length > 0"></pagination>
                    </div>
                </div>
            </div>
        </div>
    </card>
</template>

<script>
export default {
    props: ['bread'],
    data: function () {
        return {
            loading: false,
            results: [],
            total: 0,    // Total unfiltered amount of entries
            filtered: 0, // Amount of filtered entries
            layout: null,
            selected: [],
            primary: 'id', // The primary key
            parameters: {
                page: 1,
                perpage: 10,
                query: null,
                filters: {},
                order: null,
                direction: 'asc',
            },
        };
    },
    methods: {
        load: function () {
            var vm = this;

            vm.loading = true;
            axios
            .post(vm.route('voyager.'+vm.translate(vm.bread.slug, true)+'.data'), vm.parameters)
            .then(function (response) {
                for (var key in response.data){
                    if (response.data.hasOwnProperty(key) && vm.hasOwnProperty(key)) {
                        vm[key] = response.data[key];
                    }
                }

                if (vm.parameters.order === null) {
                    vm.parameters.order = vm.layout.options.default_order_column.column;
                }
            })
            .catch(function (response) {
                console.log(response.statusText);
                // TODO: Add notification
            })
            .finally(function () {
                vm.loading = false;
            });
        },
        orderBy: function (column) {
            if (this.parameters.order == column) {
                if (this.parameters.direction == 'asc') {
                    this.parameters.direction = 'desc';
                } else {
                    this.parameters.direction = 'asc';
                }
            } else {
                this.parameters.order = column;
                this.parameters.direction = 'asc';
            }
        },
        selectAll: function (checked) {
            var vm = this;
            vm.selected = [];
            if (checked) {
                vm.results.forEach(function (result) {
                    vm.selected.push(result[vm.primary]);
                });
            }
        },
        deleteEntries: function (entries, force = false) {
            var vm = this;
            vm.$notify.confirm(
                vm.trans_choice(
                    'voyager::bread.delete_type_confirm',
                    (vm.isArray(entries) ? entries.length : 1),
                    {
                        num: vm.deletableEntries,
                        types: vm.translate(vm.bread.name_plural, true),
                        type: vm.translate(vm.bread.name_singular, true)
                    }
                ),
                function (response) {
                    if (response) {
                        axios.delete(vm.route('voyager.'+vm.translate(vm.bread.slug, true)+'.delete'), {
                            params: {
                                ids: entries,
                                force: force
                            }
                        })
                        .then(function (response) {
                            vm.$notify.notify(
                                vm.trans_choice('voyager::bread.delete_type_success',
                                response.data,
                                {
                                    num: response.data,
                                    type: vm.translate(vm.bread.name_singular, true),
                                    types: vm.translate(vm.bread.name_plural, true)
                                }),
                                null,
                                'green',
                                5000
                            );
                        })
                        .catch(function (errors) {
                            //
                        })
                        .then(function () {
                            vm.load();
                        });
                    }
                },
                false,
                'red',
                vm.__('voyager::generic.yes'),
                vm.__('voyager::generic.no'),
                7500
            );
        },
        restoreEntries: function (entries) {
            var vm = this;
            vm.$notify.confirm(
                vm.trans_choice(
                    'voyager::bread.restore_type_confirm',
                    (vm.isArray(entries) ? entries.length : 1),
                    {
                        num: vm.restorableEntries,
                        types: vm.translate(vm.bread.name_plural, true),
                        type: vm.translate(vm.bread.name_singular, true)
                    }
                ),
                function (response) {
                    if (response) {
                        axios.patch(vm.route('voyager.'+vm.translate(vm.bread.slug, true)+'.restore'), {
                            ids: entries
                        })
                        .then(function (response) {
                            vm.$notify.notify(
                                vm.trans_choice('voyager::bread.restore_type_success',
                                response.data,
                                {
                                    num: response.data,
                                    type: vm.translate(vm.bread.name_singular, true),
                                    types: vm.translate(vm.bread.name_plural, true)
                                }),
                                null,
                                'green',
                                5000
                            );
                        })
                        .catch(function (errors) {
                            //
                        })
                        .then(function () {
                            vm.load();
                        });
                    }
                },
                false,
                'green',
                vm.__('voyager::generic.yes'),
                vm.__('voyager::generic.no'),
                7500
            );
        },
    },
    computed: {
        pages: function () {
            return Math.ceil(this.filtered / this.parameters.perpage);
        },
        showClearFilterButton: function () {
            if (this.parameters.query !== null && this.parameters.query !== '') {
                return true;
            }
            return Object.values(this.parameters.filters).filter(function (filter) {
                return filter !== '';
            }).length > 0;
        },
        resultDescription: function () {
            var type = this.translate(this.bread.name_plural, true);
            if (this.filtered == 1) {
                type = this.translate(this.bread.name_singular, true);
            }
            var start = 1 + ((this.parameters.page - 1) * this.parameters.perpage);
            var end = this.clamp((start + this.parameters.perpage - 1), start, this.filtered);
            var desc = this.__('voyager::bread.results_description', {
                start: start,
                end  : end,
                total: this.filtered,
                type : type,
            });

            if (this.filtered != this.total) {
                var type = this.translate(this.bread.name_plural, true);
                if (this.total == 1) {
                    type = this.translate(this.bread.name_singular, true);
                }
                desc = desc + ' ' + this.__('voyager::bread.filter_description', {
                    total: this.total,
                    type : type,
                });
            }

            return desc;
        },
        allSelected: function () {
            var vm = this;

            var not_found = false;
            vm.results.forEach(function (result) {
                if (!vm.selected.includes(result[vm.primary])) {
                    not_found = true;
                }
            });

            return !not_found;
        },
        // Returns the number of entries which are selected and are NOT soft-deleted
        deletableEntries: function () {
            var vm = this;
            return vm.results.filter(function (result) {
                return !result.is_soft_deleted && vm.selected.includes(result[vm.primary]);
            }).length;
        },
        // Returns the number of entries which are selected and ARE soft-deleted
        restorableEntries: function () {
            var vm = this;
            return vm.results.filter(function (result) {
                return result.is_soft_deleted && vm.selected.includes(result[vm.primary]);
            }).length;
        }
    },
    mounted: function () {
        var parameter_found = false;
        for (var param of this.getParametersFromUrl()) {
            try {
                var val = JSON.parse(param[1]);
                Vue.set(this.parameters, param[0], val);
            } catch {
                Vue.set(this.parameters, param[0], param[1]);
            }

            parameter_found = true;
        }

        // Data will automatically be loaded in the watcher when parameters were set above
        if (!parameter_found) {
            this.load();
        }
    },
    watch: {
        parameters: {
            handler: debounce(function (val) {
                // Remove all parameters from URL
                var url = window.location.href.split('?')[0];
                for (var key in val) {
                    if (val.hasOwnProperty(key) && val[key] !== null) {
                        if (this.isObject(val[key])) {
                            url = this.addParameterToUrl(key, JSON.stringify(val[key]), url);
                        } else {
                            url = this.addParameterToUrl(key, val[key], url);
                        }
                    }
                }
                this.pushToUrlHistory(url);
                this.load();
            }, 250),
            deep: true,
        }
    }
};
</script>