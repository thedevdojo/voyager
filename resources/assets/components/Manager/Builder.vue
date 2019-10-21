<template>
    <div>
        <div class="flex mb-4">
            <div class="w-full m-1">
                <label class="voyager-label">{{ __('voyager::generic.slug') }}</label>
                <language-input
                    class="voyager-input"
                    type="text" :placeholder="__('voyager::generic.slug')"
                    v-bind:value="bread.slug"
                    v-on:input="bread.slug = $event" />
            </div>
        </div>
        
        <div class="flex mb-4">
            <div class="w-1/2 m-1">
                <label class="voyager-label">{{ __('voyager::manager.name_singular') }}</label>
                <language-input
                    class="voyager-input"
                    type="text" :placeholder="__('voyager::manager.name_singular')"
                    v-bind:value="bread.name_singular"
                    v-on:input="bread.name_singular = $event" />
            </div>
            <div class="w-1/2 m-1">
                <label class="voyager-label">{{ __('voyager::manager.name_plural') }}</label>
                <language-input
                    class="voyager-input"
                    type="text" :placeholder="__('voyager::manager.name_plural')"
                    v-bind:value="bread.name_plural"
                    v-on:input="bread.name_plural = $event" />
            </div>
        </div>
        <div class="flex mb-4">
            <div class="w-1/3 m-1">
                <label class="voyager-label">{{ __('voyager::manager.model') }}</label>
                <input
                    class="voyager-input"
                    type="text" :placeholder="__('voyager::manager.model')"
                    v-model="bread.model">
            </div>
            <div class="w-1/3 m-1">
                <label class="voyager-label">{{ __('voyager::manager.controller') }}</label>
                <input
                    class="voyager-input"
                    type="text" :placeholder="__('voyager::manager.controller')"
                    v-model="bread.controller">
            </div>
            <div class="w-1/3 m-1">
                <label class="voyager-label">{{ __('voyager::manager.policy') }}</label>
                <input
                    class="voyager-input"
                    type="text" :placeholder="__('voyager::manager.policy')"
                    v-model="bread.policy">
            </div>
        </div>

        <div class="text-right">
            <button class="button blue" @click="saveBread()">{{ __('voyager::generic.save') }}</button>
        </div>

        <div>
            <select v-if="bread.layouts.length > 0" v-model="currentLayoutId" class="voyager-input small w-auto">
                <option v-for="(layout, i) in bread.layouts" v-bind:value="i" v-bind:key="i">
                    {{ layout.name }}
                </option>
            </select>
            <select v-if="currentLayout" @change="addFormfield" class="voyager-input small w-auto">
                <option v-bind:value="''">{{ __('voyager::manager.add_formfield') }}</option>
                <option v-for="(formfield, i) in $globals.formfields" v-bind:value="formfield.type" v-bind:key="i">
                    {{ formfield.name }}
                </option>
            </select>

            <div v-if="currentLayout && currentLayout.type == 'list'">
                <!-- {{ __('voyager::manager.soft_deletes') }} -->
                <select class="voyager-input small w-auto" v-model="currentLayout.soft_deletes">
                    <option v-bind:value="'hide'">{{ __('voyager::generic.hide') }}</option>
                    <option v-bind:value="'show'">{{ __('voyager::generic.show') }}</option>
                    <option v-bind:value="'select'">{{ __('voyager::generic.select') }}</option>
                    <option v-bind:value="'only'">{{ __('voyager::generic.only') }}</option>
                </select>

                <input type="checkbox" v-model="currentLayout.restore"> {{ __('voyager::manager.allow_restore') }}
                <input type="checkbox" v-model="currentLayout.force_delete"> {{ __('voyager::manager.allow_force_delete') }}

                <!-- {{ __('voyager::manager.soft_deletes_help') }} -->
            </div>
            <button class="button green small" @click="addLayout('view')">{{ __('voyager::manager.add_view') }}</button>
            <button class="button green small" @click="addLayout('list')">{{ __('voyager::manager.add_list') }}</button>
            <button class="button green small" @click="deleteLayout()" v-if="currentLayoutId !== null">{{ __('voyager::manager.delete_layout') }}</button>
            <div v-if="bread.layouts.length == 0">
                {{ __('voyager::manager.create_layout_first') }}
            </div>
            <div v-else-if="currentLayoutId == null">
                {{ __('voyager::manager.select_layout') }}
            </div>
            <div v-else>
                <div v-if="currentLayout.formfields.length == 0">
                    {{ __('voyager::manager.add_formfield_first') }}
                </div>
                <div v-else-if="currentLayout.type == 'view'">
                    <bread-view-builder :layout="currentLayout" :columns="columns" :computed="computed" :relationships="relationships" />
                </div>
                <div v-else-if="currentLayout.type == 'list'">
                    <bread-list-builder :layout="currentLayout" :columns="columns" :computed="computed" :relationships="relationships" />
                </div>
            </div>
        </div>
        <br><br><br>
        <textarea rows="10" v-model="jsonBread" class="voyager-input"></textarea>
    </div>
</template>

<script>
export default {
    props: ['data', 'columns', 'url', 'computed', 'relationships'],
    data: function () {
        return {
            bread: this.data,
            currentLayoutId: null,
        };
    },
    methods: {
        saveBread: function () {
            var vm = this;
            axios.put(this.url, {
                bread: vm.bread,
                _token: document.head.querySelector('meta[name="csrf-token"]').content,
            })
            .then(function (response) {
                vm.$snotify.success(vm.__('voyager::manager.bread_saved_successfully', {name: vm.bread.table}));
            })
            .catch(function (errors) {
                var errors = errors.response.data;
                Object.entries(errors).forEach(([key, val]) => {
                    val.forEach(function (e) {
                        vm.$snotify.error(e);
                    });
                });
            });
        },
        addLayout: function (type) {
            var vm = this;

            vm.$snotify.prompt(vm.__('voyager::manager.layout_name_hint'), vm.__('voyager::manager.create_layout'), {
                buttons: [
                    {
                        text: vm.__('voyager::generic.create'),
                        action: (toast) => {
                            vm.bread.layouts.push({
                                name: toast.value,
                                type: type,
                                formfields: []
                            });
                            vm.$snotify.remove(toast.id);
                            vm.currentLayoutId = vm.bread.layouts.length - 1;
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
        addFormfield: function (event) {
            if (!this.currentLayout) {
                return;
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

            return this.bread.layouts[this.currentLayoutId];
        },
        jsonBread: {
            get: function () {
                return JSON.stringify(this.bread, null, 4);
            },
            set: function (val) {
                this.bread = JSON.parse(val);
            }
        }
    },
    mounted: function () {
        if (this.bread.layouts.length > 0) {
            this.currentLayoutId = 0;
        }
        var params = {};
        location.search.substr(1).split("&").forEach(function (item) {
            params[item.split("=")[0]] = item.split("=")[1]
        });
        if (params['layout']) {
            this.currentLayoutId = params['layout'];
        }
        Vue.prototype.$language.localePicker = true;
    }
};
</script>