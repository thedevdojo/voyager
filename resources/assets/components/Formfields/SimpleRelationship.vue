<template>
    <div>
        <div v-if="action == 'browse'">
            {{ value }}
        </div>
        <div v-else-if="action == 'read'">
            <!-- TODO: Style me -->
            <div v-if="isArray(selectedOptions)">
                <span v-for="(option, key) in selectedOptions" :key="key">
                    {{ option.label }}
                </span>
            </div>
            <div v-else>
                <span>
                    {{ selectedOptions.label }}
                </span>
            </div>
        </div>
        <div v-else-if="action == 'edit' || action == 'add'">
            <div class="w-full m-1">
                <label class="voyager-label">{{ translate(options.title, true) }}</label>
                <multiselect
                    v-model="selectedOptions"
                    :options="selectOptions"
                    :placeholder="translate(options.placeholder, true)"
                    :loading="loading"
                    :internal-search="false"
                    :allow-empty="options.allow_null"
                    :disabled="!options.allow_edit"
                    track-by="key"
                    label="label"
                    :multiple="multiple"
                    :max="options.max == 0 ? Number.MAX_SAFE_INTEGER : options.max"
                    :searchable="true"
                    @search-change="debounce(loadRelationships, 250)"
                    :taggable="options.allow_tagging"
                    :tag-placeholder="__('voyager::bread.press_enter_to_create', true)"
                    :select-label="__('voyager::bread.press_enter_to_select', true)"
                    :deselect-label="__('voyager::bread.press_enter_to_deselect', true)"
                    :selected-label="__('voyager::generic.selected', true)"
                    @tag="createTag"
                >
                    <template slot="maxElements">
                        Maximum of {{ options.max }} options selected. First remove a selected option to select another.
                    </template>
                    <template slot="noResult">
                        No elements found. Consider changing the search query.
                    </template>
                    <template slot="noOptions">
                        List is empty.
                    </template>
                </multiselect>
                <p>{{ translate(options.description, true) }}</p>
            </div>
        </div>
        <div v-else-if="action == 'mockup'" class="flex mb-4">
            <div class="w-full m-1">
                <label class="voyager-label">{{ translate(options.title) }}</label>
                <select class="voyager-input" disabled>
                    <option value="" selected>{{ translate(options.placeholder) }}</option>
                </select>
                <p>{{ translate(options.description) }}</p>
            </div>
        </div>
        <div v-else-if="action == 'options'">
            <div class="flex mb-4" v-if="type == 'view'">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::generic.placeholder') }}</label>
                    <language-input
                        class="voyager-input"
                        type="text" :placeholder="__('voyager::generic.placeholder')"
                        v-bind:value="options.placeholder"
                        v-on:input="options.placeholder = $event" />
                </div>
            </div>
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::manager.display_column') }}</label>
                    <select v-model="options.column" class="voyager-input">
                        <option v-for="(column, i) in relationshipColumns" v-bind:key="i">{{ column }}</option>
                    </select>
                </div>
            </div>
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::generic.maximum') }}</label>
                    <input type="number" v-model.number="options.max" class="voyager-input">
                </div>
            </div>
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::manager.allow_null') }}</label>
                    <input type="checkbox" v-model="options.allow_null" />
                </div>
            </div>
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::manager.allow_tagging') }}</label>
                    <input type="checkbox" v-model="options.allow_tagging" />
                </div>
            </div>
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::manager.allow_editing') }}</label>
                    <input type="checkbox" v-model="options.allow_edit" />
                </div>
            </div>
        </div>
        <div v-else-if="action == 'query'">
            <slot />
        </div>
    </div>
</template>

<script>
export default {
    props: ['value', 'options', 'columns', 'column', 'action', 'type', 'relationships', 'primary', 'bread'],
    data: function () {
        return {
            selectedOptions: [],
            selectOptions: [],
            loading: false,
            loaded: false,
            multiple: false,
        };
    },
    computed: {
        relationshipColumns: function () {
            if (this.column) {
                return this.relationships[this.column].columns;
            }

            return [];
        }
    },
    methods: {
        createTag: function (tag) {
            if (this.options.allow_tagging) {
                var vm = this;
                vm.loading = true;

                axios.post(vm.route('voyager.add-relationship'), {
                    tag: tag,
                    table: vm.bread.table,
                    relationship: vm.column,
                    column: vm.options.column,
                    primary: vm.primary
                })
                .then(function (response) {
                    vm.loading = false;
                    vm.loadRelationships('', response.data.key);
                })
                .catch(function (error) {
                    vm.$snotify.error(error);
                    if (vm.debug) {
                        vm.debug(error.response.data.message, true, 'error');
                    }
                    vm.loading = false;
                });
            }
        },
        loadRelationships: function (query, key = null) {
            var vm = this;
            vm.loading = true;

            axios.post(vm.route('voyager.search-relationship'), {
                query: query,
                table: vm.bread.table,
                relationship: vm.column,
                column: vm.options.column,
                primary: vm.primary
            })
            .then(function (response) {
                if (!vm.loaded) {
                    if (response.data.multiple) {
                        vm.selectedOptions = [];
                        vm.multiple = true;
                    } else {
                        vm.selectedOptions = null;
                        vm.multiple = false;
                    }
                }
                
                response.data.results.forEach(function (result) {
                    if ((result.assigned && !vm.loaded) || (key && result.key == key)) {
                        if (response.data.multiple) {
                            vm.selectedOptions.push(result);
                        } else {
                            vm.selectedOptions = result;
                        }
                    }
                });
                vm.selectOptions = response.data.results;
                vm.loading = false;
                vm.loaded = true;
            })
            .catch(function (error) {
                vm.$snotify.error(error);
                if (vm.debug) {
                    vm.debug(error.response.data.message, true, 'error');
                }
                vm.loading = false;
            });
        }
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
        selectedOptions: function (options) {
            if (this.action !== 'read') {
                if (this.multiple) {
                    var keys = [];
                    options.forEach(function (option) {
                        keys.push(option.key);
                    });

                    this.$emit('input', keys);
                } else {
                    this.$emit('input', options.key);
                }
            }
        }
    }
};
</script>