<template>
    <div>
        <div v-if="action == 'read'">
            <div v-if="!selectedOptions">
                {{ __('voyager::generic.please_wait') }}...
            </div>
            <div v-else-if="isArray(selectedOptions)">
                <badge color="blue" v-for="(option, key) in selectedOptions" :key="key">
                    {{ option.label }}
                </badge>
            </div>
            <div v-else-if="isObject(selectedOptions)">
                <badge color="blue">
                    {{ selectedOptions.label }}
                </badge>
            </div>
        </div>
        <div v-else-if="action == 'edit' || action == 'add'">
            <div class="w-full m-1">
                <div class="flex">
                    <div class="w-2/3">
                        <label class="voyager-label bigger">{{ translate(options.title, true) }}</label>
                    </div>
                    <div class="w-1/3 text-right mb-3">
                        <modal v-if="relationshipBread && addView" ref="addModal" v-on:opened="$refs.editadd.resetForm()">
                            <bread-edit-add
                                ref="editadd"
                                :bread="relationshipBread"
                                action="add"
                                :accessors="[]"
                                :relationships="[]"
                                :layout="addView"
                                :input="{}"
                                :errors="[]"
                                :url="route('voyager.'+translate(relationshipBread.slug, true)+'.store')"
                                :from-relationship="true"
                            ></bread-edit-add>
                            <div slot="opener" class="w-full text-right">
                                <button class="button green small">
                                    {{ __('voyager::generic.add_type', {type: translate(relationshipBread.name_singular, true)}) }}
                                </button>
                            </div>
                        </modal>
                    </div>
                </div>
                <multi-select
                    :multiple="multiple"
                    :options="relationshipData"
                    :selected="selected"
                    v-on:select="selected = $event"
                    :key-prop="primary_key"
                    :display-prop="['role_name', 'id']"
                    badge-prop="role_name">
                    <modal v-if="relationshipBread && relationshipBreadList" ref="modal">
                        <h2 class="text-lg">
                            {{ __('voyager::bread.select_type', {
                                type: (multiple ? translate(relationshipBread.name_plural, true) : translate(relationshipBread.name_singular, true))
                            }) }}
                        </h2>
                        <bread-browse
                            ref="browse"
                            :bread="relationshipBread"
                            :accessors="[]"
                            :layout="relationshipBreadList"
                            :actions="{}"
                            :from-relationship="true"
                            :foreign-key="primary_key"
                            :selected="selected"
                            :multiple="multiple"
                            v-on:select="select($event)"
                        ></bread-browse>
                    </modal>
                </multi-select>
                <p class="description">{{ translate(options.description, true) }}</p>
            </div>
        </div>
        <div v-else-if="action == 'mockup'" class="flex mb-4">
            <div class="w-full m-1">
                <label class="voyager-label bigger">{{ translate(options.title) }}</label>
                <select class="voyager-input" disabled>
                    <option value="" selected>{{ translate(options.placeholder) }}</option>
                </select>
                <p class="description">{{ translate(options.description) }}</p>
            </div>
        </div>
        <div v-else-if="action == 'options'">
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::manager.display') }}</label>
                    <select v-model="options.column" class="voyager-input">
                        <optgroup :label="__('voyager::generic.column')">
                            <option v-for="(column, i) in relationshipColumns" v-bind:key="i">{{ column }}</option>
                        </optgroup>
                        <optgroup :label="__('voyager::manager.list')" v-if="relationshipBread">
                            <option v-for="(list, i) in relationshipBreadLists" v-bind:key="i" :value="'list.'+list.name">{{ list.name }}</option>
                        </optgroup>
                    </select>
                </div>
            </div>
            <div class="flex mb-4" v-if="!options.column.includes('list.')">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::manager.order_column') }}</label>
                    <select v-model="options.order_column" class="voyager-input">
                        <option v-for="(column, i) in relationshipColumns" v-bind:key="i">{{ column }}</option>
                    </select>
                </div>
            </div>
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::manager.add_view') }}</label>
                    <select v-model="options.add_view" class="voyager-input" v-if="relationshipBread">
                        <option :value="null">{{ __('voyager::generic.none') }}</option>
                        <option v-for="(view, i) in breadViews" v-bind:key="i" :value="view.uuid">{{ view.name }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['value', 'options', 'columns', 'column', 'action', 'type', 'relationships', 'primary', 'bread'],
    data: function () {
        return {
            relationshipData: [],
            selected: [],
            multiple: false,
            primary_key: '',
        };
    },
    computed: {
        relationshipColumns: function () {
            if (this.column) {
                return this.relationships[this.column].columns;
            }

            return [];
        },
        relationshipBread: function () {
            if (this.column) {
                var bread = this.relationships[this.column].bread || null;
                if (bread) {
                    bread.ajax_validation = true;
                }

                return bread;
            }

            return null;
        },
        relationshipBreadLists: function () {
            if (this.relationshipBread) {
                return this.relationshipBread.layouts.filter(function (layout) {
                    return layout.type == 'list';
                });
            }

            return [];
        },
        relationshipBreadList: function () {
            var listName = this.options.column;
            if (this.relationshipBread && listName.includes('list.')) {
                listName = listName.replace('list.', '');
                return this.relationshipBread.layouts.filter(function (layout) {
                    return layout.name == listName;
                })[0];
            }

            return null;
        },
        breadViews: function () {
            if (this.relationshipBread) {
                return this.relationshipBread.layouts.filter(function (layout) {
                    return layout.type == 'view';
                });
            }

            return [];
        },
        addView: function () {
            var vm = this;
            if (vm.relationshipBread) {
                return vm.relationshipBread.layouts.filter(function (layout) {
                    return layout.uuid == vm.options.add_view;
                })[0] || null;
            }

            return null;
        },
    },
    methods: {
        searchRelationships: function (query) {
            this.debounce(this.loadRelationships(query), 250);
        },
        loadRelationships: function (query, newkey = null) {
            var vm = this;

            axios.post(vm.route('voyager.'+vm.translate(vm.bread.slug, true)+'.relationship-data'), {
                query: query,
                relationship: vm.column,
                options: vm.options,
                primary: vm.primary,
                newkey: newkey
            })
            .then(function (response) {
                vm.multiple = response.data.multiple;
                vm.selected = response.data.selected;
                vm.relationshipData = response.data.options;
                vm.primary_key = response.data.primary;
            })
            .catch(function (error) {
                vm.$snotify.error(error);
                if (vm.debug) {
                    vm.debug(error.response.data.message, true, 'error');
                }
            });
        },
        finishAddingEntry: function (primary) {
            if (this.relationshipBread && this.addView) {
                this.$refs.addModal.close();
                this.$refs.browse.loadItems();
                this.loadRelationships('', primary);
            }
        },
        select: function (e) {
            if (e.selected) {
                if (this.multiple) {
                    this.selected.push(e.key);
                } else {
                    this.selected = e.key;
                }
            } else {
                if (this.multiple) {
                    this.selected = this.selected.filter(function(b) { return b !== e.key });
                } else {
                    this.selected = null;
                }
            }
        },
    },
    mounted: function () {
        if (this.action == 'mockup') {
            this.$parent.hasColumn = false;
            this.$parent.hasRelationship = true;
        } else if (this.action !== 'options') {
            this.loadRelationships();
        }
    },
    watch: {
        selected: function (selected) {
            this.$emit('input', this.selected);
        }
    }
};
</script>