<template>
    <div>
        <div class="flex mb-4">
            <div class="w-full m-1">
                <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('voyager::generic.slug') }}</label>
                <language-input
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    type="text" :placeholder="__('voyager::generic.slug')"
                    v-bind:value="bread.slug"
                    v-on:input="bread.slug = $event" />
            </div>
        </div>
        
        <div class="flex mb-4">
            <div class="w-1/2 m-1">
                <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('voyager::manager.name_singular') }}</label>
                <language-input
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    type="text" :placeholder="__('voyager::manager.name_singular')"
                    v-bind:value="bread.name_singular"
                    v-on:input="bread.name_singular = $event" />
            </div>
            <div class="w-1/2 m-1">
                <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('voyager::manager.name_plural') }}</label>
                <language-input
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    type="text" :placeholder="__('voyager::manager.name_plural')"
                    v-bind:value="bread.name_plural"
                    v-on:input="bread.name_plural = $event" />
            </div>
        </div>
        <div class="flex mb-4">
            <div class="w-1/3 m-1">
                <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('voyager::manager.model_name') }}</label>
                <input
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    type="text" :placeholder="__('voyager::manager.model_name')"
                    v-model="bread.model_name">
            </div>
            <div class="w-1/3 m-1">
                <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('voyager::manager.controller') }}</label>
                <input
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    type="text" :placeholder="__('voyager::manager.controller')"
                    v-model="bread.controller">
            </div>
            <div class="w-1/3 m-1">
                <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('voyager::manager.policy') }}</label>
                <input
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    type="text" :placeholder="__('voyager::manager.policy')"
                    v-model="bread.policy">
            </div>
        </div>

        <div class="text-right">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" @click="saveBread()">{{ __('voyager::generic.save') }}</button>
        </div>

        <div>
            <select v-model="currentLayoutId">
                <option v-for="(layout, i) in bread.layouts" v-bind:value="i" v-bind:key="i">
                    {{ layout.name }}
                </option>
            </select>
            <select v-if="currentLayout" @change="addFormfield">
                <option v-bind:value="''">{{ __('voyager::manager.add_formfield') }}</option>
                <option v-for="(formfield, i) in $eventHub.formfields" v-bind:value="formfield.type" v-bind:key="i">
                    {{ formfield.name }}
                </option>
            </select>
            <button class="bg-green-500 text-white font-bold py-2 px-4 rounded" @click="addLayout('view')">{{ __('voyager::manager.add_view') }}</button>
            <button class="bg-green-500 text-white font-bold py-2 px-4 rounded" @click="addLayout('list')">{{ __('voyager::manager.add_list') }}</button>
            <button class="bg-green-500 text-white font-bold py-2 px-4 rounded" @click="deleteLayout()" v-if="currentLayoutId !== null">{{ __('voyager::manager.delete_layout') }}</button>
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
                    <bread-view-builder :layout="currentLayout" :fields="fields" />
                </div>
                <div v-else-if="currentLayout.type == 'list'">
                    <bread-list-builder :layout="currentLayout" :fields="fields" />
                </div>
            </div>
        </div>
        <br><br><br>
        <textarea class="w-full" rows="20">{{ bread }}</textarea>
    </div>
</template>

<script>
export default {
    props: ['data', 'fields', 'url'],
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
                if (response.data.success) {
                    vm.$snotify.success(response.data.message);
                } else {
                    vm.$snotify.error(response.data.message);
                }
            })
            .catch(function (error) {
                vm.$snotify.error(error);
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

            var formfield = this.$eventHub.formfields.filter(function (formfield) {
                return formfield.type == event.target.value;
            }).pop();

            if (formfield) {
                this.currentLayout.formfields.push({
                    type: formfield.type,
                    field: '',
                    options: formfield.options,
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
        }
    },
    mounted: function () {
        if (this.bread.layouts.length > 0) {
            this.currentLayoutId = 0;
        }
    }
};
</script>