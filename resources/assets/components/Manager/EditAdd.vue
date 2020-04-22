<template>
    <div>
        <card title="Edit BREAD" icon="bread" :icon-size="8">
            <div slot="actions">
                <div class="flex items-center">
                    <button class="button green" @click="loadProperties">
                        <icon icon="sync" class="rotating-ccw" :size="4" v-if="loadingProps" />
                        {{ __('voyager::manager.reload_properties') }}
                    </button>
                    <locale-picker :small="false" />
                </div>
            </div>
            <div>
                <alert color="yellow" v-if="!bread.model || bread.model == ''">
                    <span slot="title">
                        {{ __('voyager::generic.heads_up') }}
                    </span>
                    {{ __('voyager::manager.new_breads_prop_warning') }}
                </alert>
                <div class="flex mb-4">
                    <div class="w-full m-1">
                        <label class="label" for="slug">{{ __('voyager::generic.slug') }}</label>
                        <language-input
                            class="voyager-input w-full"
                            id="slug"
                            type="text" :placeholder="__('voyager::generic.slug')"
                            v-bind:value="bread.slug"
                            v-on:input="bread.slug = $event" />
                    </div>
                </div>
                
                <div class="flex-none md:flex mb-4">
                    <div class="w-full md:w-1/4 m-1">
                        <label class="label" for="name-singular">{{ __('voyager::manager.name_singular') }}</label>
                        <language-input
                            class="voyager-input w-full"
                            id="name-singular"
                            type="text" :placeholder="__('voyager::manager.name_singular')"
                            v-bind:value="bread.name_singular"
                            v-on:input="bread.name_singular = $event" />
                    </div>
                    <div class="w-full md:w-1/4 m-1">
                        <label class="label" for="name-plural">{{ __('voyager::manager.name_plural') }}</label>
                        <language-input
                            class="voyager-input w-full"
                            id="name-plural"
                            type="text" :placeholder="__('voyager::manager.name_plural')"
                            v-bind:value="bread.name_plural"
                            v-on:input="bread.name_plural = $event" />
                    </div>
                    <div class="w-full md:w-1/6 m-1">
                        <label class="label" for="icon">{{ __('voyager::generic.icon') }}</label>
                        <modal ref="icon_modal" :title="__('voyager::generic.select_icon')">
                            <icon-picker v-on:select="$refs.icon_modal.close(); bread.icon = $event" />
                            <div slot="opener" class="w-full">
                                <button class="button green icon-only">
                                    <icon class="cursor-pointer text-white my-1 content-center" :size="6" :icon="bread.icon" />
                                </button>
                            </div>
                        </modal>
                    </div>
                    <div class="w-full md:w-1/6 m-1">
                        <label class="label" for="icon">{{ __('voyager::manager.show_menu_badge') }}</label>
                        <input type="checkbox" class="voyager-input" v-model="bread.badge">
                    </div>
                    <div class="w-full md:w-1/6 m-1">
                        <label class="label" for="color">{{ __('voyager::generic.color') }}</label>

                        <modal ref="color_modal" :title="__('voyager::generic.select_color')" icon="palette">
                            <color-picker v-on:select="$refs.color_modal.close(); bread.color = $event"></color-picker>
                            <div slot="opener" class="w-full">
                                <button :class="bread.color" class="button">{{ ucfirst(bread.color) }}</button>
                            </div>
                        </modal>
                    </div>
                </div>
                <div class="flex-none md:flex mb-4">
                    <div class="w-full md:w-1/3 m-1">
                        <label class="label" for="model">{{ __('voyager::manager.model') }}</label>
                        <input
                            class="voyager-input w-full"
                            id="model"
                            type="text" :placeholder="__('voyager::manager.model')"
                            v-model="bread.model">
                    </div>
                    <div class="w-full md:w-1/3 m-1">
                        <label class="label" for="controller">{{ __('voyager::manager.controller') }}</label>
                        <input
                            class="voyager-input w-full"
                            id="controller"
                            type="text" :placeholder="__('voyager::manager.controller')"
                            v-model="bread.controller">
                    </div>
                    <div class="w-full md:w-1/3 m-1">
                        <label class="label" for="policy">{{ __('voyager::manager.policy') }}</label>
                        <input
                            class="voyager-input w-full"
                            id="policy"
                            type="text" :placeholder="__('voyager::manager.policy')"
                            v-model="bread.policy">
                    </div>
                </div>
                <div class="flex-none md:flex mb-4">
                    <div class="w-full md:w-1/2 m-1">
                        <label class="label" for="scope">{{ __('voyager::manager.scope') }}</label>
                        <select class="voyager-input w-full" v-model="bread.scope">
                            <option :value="null">{{ __('voyager::generic.none') }}</option>
                            <option v-for="(scope, i) in scopes" :key="i">{{ scope }}</option>
                        </select>
                    </div>
                    <div class="w-full md:w-1/2 m-1">
                        <label class="label" for="global_search">{{ __('voyager::manager.global_search_display_field') }}</label>
                        <select class="voyager-input w-full" v-model="bread.global_search_field">
                            <option :value="null">{{ __('voyager::generic.none') }}</option>
                            <option v-for="column in columns" :key="column">{{ column }}</option>
                        </select>
                    </div>
                </div>
                <div class="w-full">
                    <button class="button blue" @click="saveBread">
                        <icon icon="sync" class="mr-0 md:mr-1 rotating-ccw" :size="4" v-if="savingBread" />
                        {{ __('voyager::generic.save') }}
                    </button>
                    <button class="button green" @click="backupBread">
                        <icon icon="sync" class="mr-0 md:mr-1 rotating-ccw" :size="4" v-if="backingUp" />
                        {{ __('voyager::generic.backup') }}
                    </button>
                </div>
            </div>
        </card>

        <card :show-header="false">
            <!-- Toolbar -->
            <div class="w-full mb-5 flex">
                <select class="voyager-input small" v-model="currentLayoutName" :disabled="bread.layouts.length == 0">
                    <option :value="null" v-if="bread.layouts.length == 0">
                        {{ __('voyager::manager.create_layout_first') }}
                    </option>
                    <optgroup label="Views" v-if="views.length > 0">
                        <option v-for="view in views" :key="'view-' + view.name">{{ view.name }}</option>
                    </optgroup>
                    <optgroup label="Lists" v-if="lists.length > 0">
                        <option v-for="list in lists" :key="'list-' + list.name">{{ list.name }}</option>
                    </optgroup>
                </select>
                <div class="dropdown left ml-2" v-click-outside="closeFormfieldAddDropdown">
                    <button @click="addFormfieldDropdownOpen = !addFormfieldDropdownOpen"
                            class="button green small"
                            :disabled="bread.layouts.length == 0">
                        <icon icon="list-ul" />
                        <span>
                            {{ __('voyager::manager.add_formfield') }}
                        </span>
                    </button>
                    <slide-y-up-transition>
                        <div class="body w-64" v-if="addFormfieldDropdownOpen">
                            <div class="py-1">
                                <a v-for="formfield in formfields"
                                    :key="'formfield-'+formfield.type"
                                    href="#"
                                    @click.prevent="addFormfield(formfield)"
                                    class="block px-4 py-2 text-base leading-5 focus:outline-none">
                                    {{ formfield.name }}
                                </a>
                                <a
                                    :href="route('voyager.plugins.index')+'/?type=formfield'"
                                    target="_blank"
                                    class="italic block px-4 py-3 text-base leading-5 focus:outline-none">
                                    {{ __('voyager::manager.formfields_more') }}
                                </a>
                            </div>
                        </div>
                    </slide-y-up-transition>
                </div>
                <button class="button blue small" @click="addLayout(false)">
                    <icon icon="list-ul" />
                    <span>{{ __('voyager::manager.add_list') }}</span>
                </button>
                <button class="button blue small" @click="addLayout(true)">
                    <icon icon="apps" />
                    <span>{{ __('voyager::manager.add_view') }}</span>
                </button>
                <button class="button yellow small" @click="renameLayout" :disabled="!currentLayout">
                    <icon icon="pen" />
                    <span>{{ __('voyager::manager.rename_layout') }}</span>
                </button>
                <button class="button red small" @click="deleteLayout" :disabled="!currentLayout">
                    <icon icon="trash" />
                    <span>
                        {{ __('voyager::manager.delete_layout') }}
                    </span>
                </button>
                <button class="button blue small" @click="layoutOptionsOpen = true" :disabled="!currentLayout">
                    <icon icon="cog" />
                    <span>
                        {{ __('voyager::generic.options') }}
                    </span>
                </button>
                <slide-in v-if="currentLayout" :opened="layoutOptionsOpen" width="w-1/3" class="text-left" v-on:closed="layoutOptionsOpen = false">
                    <div class="flex w-full mb-3">
                        <div class="w-1/2 text-2xl">
                            <h4>{{ __('voyager::generic.options') }}</h4>
                        </div>
                        <div class="w-1/2 flex justify-end">
                            <locale-picker v-if="$language.localePicker" />
                            <button class="button green icon-only" @click="layoutOptionsOpen = false">
                                <icon icon="times" />
                            </button>
                        </div>
                    </div>
                    <label class="label mt-4">{{ __('voyager::manager.show_soft_deleted') }}</label>
                    <input type="checkbox" v-model="currentLayout.options.soft_deletes">
                </slide-in>
            </div>

            <div class="card text-center text-xl" v-if="!currentLayout">
                {{ __('voyager::manager.create_select_layout') }}
            </div>
            <div class="card text-center text-xl" v-else-if="currentLayout && currentLayout.formfields.length == 0">
                {{ __('voyager::manager.add_formfield_to_layout') }}
            </div>
            <component
                v-else-if="currentLayout"
                :is="'bread-manager-' + currentLayout.type"
                :computed="computed"
                :columns="columns"
                :relationships="relationships"
                :formfields="currentLayout.formfields"
                :available-formfields="formfields"
                :options="currentLayout.options"
                :options-id="openOptionsId"
                v-on:delete="deleteFormfield($event)"
                v-on:formfields="currentLayout.formfields = $event"
                v-on:options="currentLayout.options = $event"
                v-on:open-options="openOptionsId = $event" />
        </card>

        <collapsible v-if="debug" :title="__('voyager::manager.json_output')">
            <textarea class="input w-full" rows="10" v-model="jsonBread"></textarea>
        </collapsible>
    </div>
</template>

<script>
export default {
    props: ['data', 'formfields'],
    data: function () {
        return {
            bread: this.data,
            computed: [],
            columns: [],
            scopes: [],
            relationships: [],
            softdeletes: false,
            loadingProps: false,
            savingBread: false,
            backingUp: false,
            currentLayoutName: null,
            addFormfieldDropdownOpen: false,
            openOptionsId: null,
            layoutOptionsOpen: false,
        };
    },
    methods: {
        saveBread: function () {
            var vm = this;

            vm.savingBread = true;

            axios.put(this.route('voyager.bread.update', this.bread.table), {
                bread: vm.bread
            })
            .then(function (response) {
                vm.$notify.notify(
                    vm.__('voyager::manager.bread_saved_successfully'),
                    null, 'green', 5000
                );
            })
            .catch(function (errors) {
                var errors = errors.response.data;
                if (!vm.isObject(errors)) {
                    vm.$notify.notify(
                        errors,
                        null, 'red', 5000
                    );
                } else {
                    Object.entries(errors).forEach(([key, val]) => {
                        val.forEach(function (e) {
                            vm.$notify.notify(
                                e,
                                null, 'red', 5000
                            );
                        });
                    });
                }
            }).then(function () {
                vm.savingBread = false;
            });
        },
        backupBread: function () {
            var vm = this;
            vm.backingUp = true;
            axios.post(vm.route('voyager.bread.backup-bread'), {
                table: vm.bread.table
            })
            .then(function (response) {
                vm.$notify.notify(vm.__('voyager::manager.bread_backed_up', { name: response.data }), null, 'blue', 5000);
            })
            .catch(function (error) {
                vm.$notify.notify(error.response.statusText, null, 'red', 5000);
            })
            .then(function () {
                vm.backingUp = false;
            });
        },
        loadProperties: function () {
            var vm = this;

            if (vm.loadingProps) {
                return;
            }

            vm.loadingProps = true;
            axios.post(vm.route('voyager.bread.get-properties'), {
                model: vm.bread.model,
                resolve_relationships: true,
            })
            .then(function (response) {
                Object.keys(response.data).map(function(key) {
                    Vue.set(vm, key, response.data[key]);
                });
            })
            .catch(function (error) {
                vm.$notify.notify(
                    error.response.data,
                    null, 'red', 5000
                );
            })
            .then(function () {
                vm.loadingProps = false;
            });
        },
        addLayout: function (view) {
            var vm = this;

            vm.$notify.prompt(
                vm.__('voyager::manager.enter_name'), '',
                function (value) {
                    if (value && value !== '') {
                        var filtered = vm.bread.layouts.filter(function (layout) {
                            return layout.name == value;
                        });

                        if (filtered.length > 0) {
                            vm.$notify.notify(
                                vm.__('voyager::manager.name_already_exists'),
                                null, 'red', 5000
                            );
                            return;
                        }

                        var view_options = {};
                        var list_options = {
                            default_order_column: {
                                column: null,
                                type: null,
                            },
                            soft_deletes: true,
                        };

                        vm.bread.layouts.push({
                            name: value,
                            type: (view ? 'view' : 'list'),
                            options: (view ? view_options : list_options),
                            formfields: []
                        });

                        vm.currentLayoutName = value;
                    }
                },
                'blue', vm.__('voyager::generic.ok'), vm.__('voyager::generic.cancel'), false, 7500
            );
        },
        renameLayout: function () {
            var vm = this;
            vm.$notify.prompt(
                vm.__('voyager::manager.enter_new_name'), vm.currentLayoutName,
                function (value) {
                    if (value && value !== '') {
                        if (value == vm.currentLayoutName) {
                            return;
                        }
                        var filtered = vm.bread.layouts.filter(function (layout) {
                            return layout.name == value;
                        });

                        if (filtered.length > 0) {
                            vm.$notify.notify(
                                vm.__('voyager::manager.name_already_exists'),
                                null, 'red', 5000
                            );
                            return;
                        }

                        vm.currentLayout.name = value;
                        vm.currentLayoutName = value;
                    }
                },
                'blue', vm.__('voyager::generic.ok'), vm.__('voyager::generic.cancel'), false, 7500
            );
        },
        deleteLayout: function () {
            var vm = this;
             vm.$notify.confirm(
                vm.__('voyager::manager.delete_layout_confirm'),
                function (result) {
                    if (result) {
                        var name = vm.currentLayoutName;
                        vm.currentLayoutName = null;
                        vm.bread.layouts = vm.bread.layouts.filter(function (layout) {
                            return layout.name !== name;
                        });

                        if (vm.bread.layouts.length > 0) {
                            vm.currentLayoutName = vm.bread.layouts[0].name;
                        }
                    }
                }, false, 'yellow', vm.__('voyager::generic.yes'), vm.__('voyager::generic.no'), 7500
            );
        },
        addFormfield: function (formfield) {
            this.closeFormfieldAddDropdown();

            // Merge any global options into the below options
            var listOptions = formfield.listOptions;
            var viewOptions = formfield.viewOptions;

            viewOptions.width = 'w-3/6';

            var formfield = {
                type: formfield.type,
                column: {
                    column: null,
                    type: null,
                },
                translatable: false,
                canbetranslated: formfield.canbetranslated,
                options: JSON.parse(JSON.stringify(this.currentLayout.type == 'list' ? listOptions : viewOptions)),
                validation: [],
            };

            if (this.currentLayout.type == 'list') {
                formfield.title = null;
            }

            this.currentLayout.formfields.push(formfield);
        },
        deleteFormfield: function (key) {
            var vm = this;

            vm.$notify.confirm(
                vm.__('voyager::manager.delete_formfield_confirm'),
                function (result) {
                    if (result) {
                        vm.currentLayout.formfields.splice(key, 1);
                    }
                }, false, 'yellow', vm.__('voyager::generic.yes'), vm.__('voyager::generic.no'), 7500
            );
        },
        closeFormfieldAddDropdown: function () {
            this.addFormfieldDropdownOpen = false;
        },
    },
    computed: {
        views: function () {
            return this.bread.layouts.filter(function (layout) {
                return layout.type == 'view';
            });
        },
        lists: function () {
            return this.bread.layouts.filter(function (layout) {
                return layout.type == 'list';
            });
        },
        currentLayout: function () {
            var vm = this;
            return this.bread.layouts.filter(function (layout, key) {
                if (layout.name == vm.currentLayoutName) {
                    vm.pushToUrlHistory(vm.addParameterToUrl('layout', key));
                    return true;
                }
                return false;
            })[0];
        },
        jsonBread: {
            get: function () {
                return JSON.stringify(this.bread, null, 2);
            },
            set: function (value) {
                
            }
        },
    },
    mounted: function () {
        var vm = this;
        Vue.prototype.$language.localePicker = true;

        // Load model-properties (only when we already know the model-name)
        if (vm.bread.model) {
            vm.loadProperties();
        }

        document.addEventListener('keydown', function (e) {
            if (event.ctrlKey && event.key === 's') {
                e.preventDefault();
                vm.saveBread();
            }
        });
    },
    created: function () {
        var layout = parseInt(this.getParameterFromUrl('layout', 0));
        if (this.bread.layouts.length >= (layout+1)) {
            this.currentLayoutName = this.bread.layouts[layout].name;
        }
    },
};
</script>