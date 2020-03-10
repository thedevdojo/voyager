<template>
    <div>
        <div class="voyager-card">
            <div class="body">
                <div class="flex mb-4">
                    <div class="w-full m-1">
                        <label class="voyager-label" for="slug">{{ __('voyager::generic.slug') }}</label>
                        <language-input
                            class="voyager-input"
                            id="slug"
                            type="text" :placeholder="__('voyager::generic.slug')"
                            v-bind:value="bread.slug"
                            v-on:input="bread.slug = $event" />
                    </div>
                </div>
                
                <div class="flex mb-4">
                    <div class="w-1/2 m-1">
                        <label class="voyager-label" for="name-singular">{{ __('voyager::manager.name_singular') }}</label>
                        <language-input
                            class="voyager-input"
                            id="name-singular"
                            type="text" :placeholder="__('voyager::manager.name_singular')"
                            v-bind:value="bread.name_singular"
                            v-on:input="bread.name_singular = $event" />
                    </div>
                    <div class="w-1/2 m-1">
                        <label class="voyager-label" for="name-plural">{{ __('voyager::manager.name_plural') }}</label>
                        <language-input
                            class="voyager-input"
                            id="name-plural"
                            type="text" :placeholder="__('voyager::manager.name_plural')"
                            v-bind:value="bread.name_plural"
                            v-on:input="bread.name_plural = $event" />
                    </div>
                </div>
                <div class="flex mb-4">
                    <div class="w-1/3 m-1">
                        <label class="voyager-label" for="model">{{ __('voyager::manager.model') }}</label>
                        <input
                            class="voyager-input"
                            id="model"
                            type="text" :placeholder="__('voyager::manager.model')"
                            v-model="bread.model">
                    </div>
                    <div class="w-1/3 m-1">
                        <label class="voyager-label" for="controller">{{ __('voyager::manager.controller') }}</label>
                        <input
                            class="voyager-input"
                            id="controller"
                            type="text" :placeholder="__('voyager::manager.controller')"
                            v-model="bread.controller">
                    </div>
                    <div class="w-1/3 m-1">
                        <label class="voyager-label" for="policy">{{ __('voyager::manager.policy') }}</label>
                        <input
                            class="voyager-input"
                            id="policy"
                            type="text" :placeholder="__('voyager::manager.policy')"
                            v-model="bread.policy">
                    </div>
                </div>
                <div class="flex mb-4">
                    <div class="w-1/3 m-1">
                        <label class="voyager-label" for="scope">{{ __('voyager::manager.scope') }}</label>
                        <select class="voyager-input" v-model="bread.scope">
                            <option :value="null">{{ __('voyager::generic.none') }}</option>
                            <option v-for="(scope, i) in scopes" :key="i">{{ scope }}</option>
                        </select>
                    </div>
                    <div class="w-1/3 m-1">
                        <label class="voyager-label" for="global_search">{{ __('voyager::manager.global_search_display_field') }}</label>
                        <select class="voyager-input" v-model="bread.global_search_field">
                            <option v-for="(col, i) in allColumns" :key="i">{{ col }}</option>
                        </select>
                    </div>
                    <div class="w-1/3 m-1">
                        <label class="voyager-label" for="ajax_validation">{{ __('voyager::manager.ajax_validation') }}</label>
                        <select class="voyager-input" v-model="bread.ajax_validation">
                            <option :value="true">{{ __('voyager::generic.yes') }}</option>
                            <option :value="false">{{ __('voyager::generic.no') }}</option>
                        </select>
                    </div>
                </div>
                <div class="text-right">
                    <button class="button blue" @click="saveBread()">{{ __('voyager::generic.save') }}</button>
                </div>
            </div>
        </div>

        <div class="voyager-card">
            <div class="body">
                <div class="flex">
                    <select v-if="bread.layouts.length > 0" v-model="currentLayoutId" class="voyager-input small w-auto mr-1">
                        <option v-for="(layout, i) in bread.layouts" v-bind:value="i" v-bind:key="i">
                            {{ layout.name }}
                        </option>
                    </select>
                    <select v-if="currentLayout" @change="addFormfield" class="voyager-input small w-auto">
                        <option v-bind:value="''">{{ __('voyager::manager.add_formfield') }}</option>
                        <option v-for="(formfield, i) in getFormfields()" v-bind:value="formfield.type" v-bind:key="i">
                            {{ formfield.name }}
                        </option>
                    </select>
                    <button class="button green small" @click="addLayout('view')">{{ __('voyager::manager.add_view') }}</button>
                    <button class="button green small" @click="addLayout('list')">{{ __('voyager::manager.add_list') }}</button>
                    <button class="button red small" @click="deleteLayout()" v-if="currentLayoutId !== null">{{ __('voyager::manager.delete_layout') }}</button>
                    <button class="button blue small" @click="showLayoutSettings = true" v-if="currentLayoutId !== null">{{ __('voyager::manager.layout_settings') }}</button>
                    <button class="button blue small" @click="saveBread()">{{ __('voyager::generic.save') }}</button>
                </div>
                <div v-if="bread.layouts.length == 0" class="w-full text-center text-xl mt-6">
                    {{ __('voyager::manager.create_layout_first') }}
                </div>
                <div v-else-if="currentLayoutId == null" class="w-full text-center text-xl mt-6">
                    {{ __('voyager::manager.select_layout') }}
                </div>
                <div v-else class="mt-4">
                    <div v-if="currentLayout.formfields.length == 0" class="w-full text-center text-xl mt-6">
                        {{ __('voyager::manager.add_formfield_first') }}
                    </div>
                    <div v-else-if="currentLayout.type == 'view'">
                        <bread-view-builder
                            :layout="currentLayout"
                            :columns="columns"
                            :computed="computed"
                            :relationships="relationships"
                            :show-settings="showLayoutSettings"
                            v-on:layout-settings-closed="showLayoutSettings = false" />
                    </div>
                    <div v-else-if="currentLayout.type == 'list'">
                        <bread-list-builder
                            :layout="currentLayout"
                            :columns="columns"
                            :computed="computed"
                            :relationships="relationships"
                            :show-settings="showLayoutSettings"
                            v-on:layout-settings-closed="showLayoutSettings = false" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['data', 'columns', 'url', 'computed', 'relationships', 'scopes', 'new'],
    data: function () {
        return {
            bread: this.data,
            currentLayoutId: null,
            showLayoutSettings: false,
        };
    },
    methods: {
        saveBread: function () {
            var vm = this;
            var bread = vm.bread;
            axios.put(this.url, {
                bread: vm.bread,
                _token: vm.$globals.csrf_token,
            })
            .then(function (response) {
                vm.$snotify.success(vm.__('voyager::manager.bread_saved_successfully', {name: vm.bread.table}));
            })
            .catch(function (errors) {
                var errors = errors.response.data;
                if (!vm.isObject(errors)) {
                    vm.$snotify.error(errors);
                } else {
                    Object.entries(errors).forEach(([key, val]) => {
                        val.forEach(function (e) {
                            vm.$snotify.error(e);
                        });
                    });
                }
            });
        },
        addLayout: function (type) {
            var vm = this;

            vm.$snotify.prompt(vm.__('voyager::manager.layout_name_hint'), vm.__('voyager::manager.create_layout'), {
                buttons: [
                    {
                        text: vm.__('voyager::generic.create'),
                        action: (toast) => {
                            if (toast.value && toast.value !== '') {
                                var double_named = vm.bread.layouts.filter(function (layout) {
                                    return layout.name == toast.value;
                                });
                                if (double_named.length > 0) {
                                    vm.$snotify.error(vm.__('voyager::manager.layout_name_already_used', {name: toast.value}));
                                    return;
                                }
                                vm.bread.layouts.push({
                                    name: toast.value,
                                    type: type,
                                    uuid: vm.uuid(),
                                    formfields: []
                                });
                                vm.currentLayoutId = vm.bread.layouts.length - 1;
                            }
                            
                            vm.$snotify.remove(toast.id);
                        }
                    },
                    {
                        text: vm.__('voyager::generic.cancel'),
                        action: (toast) => {
                            vm.$snotify.remove(toast.id);
                        }
                    },
                ],
                placeholder: vm.__('voyager::manager.layout_name')
            });
        },
        deleteLayout: function () {
            var vm = this;

            vm.$snotify.confirm(vm.__('voyager::manager.delete_layout_confirm'), vm.__('voyager::manager.delete_layout'), {
                timeout: 5000,
                showProgressBar: true,
                closeOnClick: false,
                pauseOnHover: true,
                buttons: [
                    {
                        text: vm.__('voyager::generic.yes'),
                        action: (toast) => {
                            var id = vm.currentLayoutId;
                            vm.currentLayoutId = null;
                            vm.bread.layouts.splice(id, 1);
                            if (vm.bread.layouts.length > 0) {
                                vm.currentLayoutId = 0;
                            }
                            vm.$snotify.remove(toast.id);
                        }
                    },
                    {
                        text: vm.__('voyager::generic.no'),
                        action: (toast) => {
                            vm.$snotify.remove(toast.id);
                        }
                    },
                ]
            });
        },
        getFormfields: function () {
            var vm = this;
            return this.$globals.formfields.filter(function (formfield) {
                if (vm.currentLayout.type == 'list') {
                    return formfield.lists;
                }

                return formfield.views;
            });
        },
        addFormfield: function (event) {
            if (!this.currentLayout) {
                return;
            }

            if (this.new && event.target.value.includes('relationship')) {
                this.$snotify.warning(this.__('voyager::manager.new_bread_relation_warn'));

                return;
            }

            if (event.target.value.includes('relationship') && this.relationships.length == 0) {
                this.$snotify.warning(this.__('voyager::manager.no_relationships'));
            }

            var formfield = this.$globals.formfields.filter(function (formfield) {
                return formfield.type == event.target.value;
            }).pop();

            if (formfield) {
                var options = JSON.parse(JSON.stringify(formfield.options));
                options.link = false;
                this.currentLayout.formfields.push({
                    type: formfield.type,
                    column: '',
                    options: options,
                    rules: []
                });
            }

            event.target.value = '';
        },
        deleteFormfield: function (id) {
            var vm = this;
            vm.$snotify.confirm(vm.__('voyager::manager.delete_formfield_confirm'), vm.__('voyager::manager.delete_formfield'), {
                timeout: 5000,
                showProgressBar: true,
                closeOnClick: false,
                pauseOnHover: true,
                buttons: [
                    {
                        text: vm.__('voyager::generic.yes'),
                        action: (toast) => {
                            vm.currentLayout.formfields.splice(id, 1);
                            vm.$snotify.remove(toast.id);
                            vm.$forceUpdate();
                        }
                    }, {
                        text: vm.__('voyager::generic.no'),
                        action: (toast) => {
                            vm.$snotify.remove(toast.id);
                        }
                    },
                ]
            });
        },
    },
    computed: {
        currentLayout: function () {
            if (this.currentLayoutId == null) {
                return null;
            }
            this.pushToUrlHistory(this.addParameterToUrl('layout', this.currentLayoutId));

            return this.bread.layouts[this.currentLayoutId];
        },
        allColumns: function () {
            return this.columns.concat(this.computed).sort();
        }
    },
    mounted: function () {
        if (this.bread.layouts.length > 0) {
            this.currentLayoutId = 0;

            this.bread.layouts.forEach(function (layout) {
                layout.formfields.forEach(function (formfield) {
                    delete formfield.views;
                    delete formfield.lists;
                    delete formfield.settings;
                });
            });

            Vue.set(this, 'bread', this.bread);
        }
        var layoutid = this.getParameterFromUrl('layout');
        if (layoutid) {
            this.currentLayoutId = layoutid;
        }
        Vue.prototype.$language.localePicker = true;
    }
};
</script>