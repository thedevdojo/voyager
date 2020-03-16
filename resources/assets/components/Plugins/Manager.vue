<template>
    <div>
        <modal>
            <div class="flex mb-4">
                <div class="w-2/3">
                    <h4 class="text-gray-100 text-xl">{{ __('voyager::plugins.plugins') }}</h4>
                </div>
                <div class="w-1/3 text-right text-gray-100">
                    <button class="button green close-button">X</button>
                </div>
            </div>
            <input type="text" class="voyager-input w-full mb-3" v-model="query" :placeholder="__('voyager::generic.search')">
            <div v-for="(plugin, i) in filteredPlugins.slice(start, end)" class="text-white" :key="'plugin-'+i">
                <div class="flex">
                    <div class="w-3/5">
                        <h3 class="text-gray-100 text-lg">{{ plugin.name }}</h3>
                        <p>{{ plugin.description }}</p>
                        <a v-if="plugin.website" :href="plugin.website" target="_blank">
                            {{ __('voyager::generic.website') }}
                        </a>
                        <span v-else>&nbsp;</span>
                    </div>
                    <div class="w-2/5 text-right">
                        <input class="voyager-input select-none" :value="'composer require '+plugin.repository" @dblclick="copy(plugin)">
                    </div>
                </div>
                <hr class="w-full bg-gray-300 my-4">
            </div>
            <div class="w-full text-right">
                <div class="button-group">
                    <button class="button blue" v-for="i in pages" @click="page = (i - 1)" :key="'page-button-'+i">{{ i }}</button>
                </div>
            </div>
            <div slot="opener" class="w-full text-right mb-5">
                <button class="button green">{{ __('voyager::plugins.find_a_plugin') }}</button>
            </div>
        </modal>

        <div v-if="hasMultiplePlugins('auth')" class="alert red mb-2" v-html="nl2br(__('voyager::plugins.multiple_auth_plugins'))"></div>
        <div v-if="hasMultiplePlugins('menu')" class="alert red mb-2" v-html="nl2br(__('voyager::plugins.multiple_menu_plugins'))"></div>

        <div class="voyager-table striped">
            <table v-bind:class="[loading ? 'loading' : '']" id="bread-manager-browse">
                <thead>
                    <tr>
                        <th>
                            {{ __('voyager::generic.name') }}
                        </th>
                        <th>
                            {{ __('voyager::generic.description') }}
                        </th>
                        <th>
                            {{ __('voyager::generic.type') }}
                        </th>
                        <th>
                            {{ __('voyager::generic.version') }}
                        </th>
                        <th class="text-right">
                            {{ __('voyager::generic.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(plugin, i) in installedPlugins" :key="'installed-plugin-'+i">
                        <td>{{ translate(plugin.name) }}</td>
                        <td>{{ translate(plugin.description) }}</td>
                        <td>{{ __('voyager::plugins.types.'+plugin.type) }}</td>
                        <td>
                            {{ plugin.version || '-' }}
                        </td>
                        <td class="text-right">
                            <a class="button green" v-if="plugin.website" :href="plugin.website" target="_blank">
                                {{ __('voyager::generic.website') }}
                            </a>
                            <button v-if="!plugin.enabled" class="button green" @click="enablePlugin(plugin, true)">
                                {{ __('voyager::generic.enable') }}
                            </button>
                            <button v-else class="button red" @click="enablePlugin(plugin, false)">
                                {{ __('voyager::generic.disable') }}
                            </button>
                            <a v-if="plugin.has_settings && plugin.enabled" :href="route('voyager.plugins.settings', i)" class="button blue">
                                {{ __('voyager::generic.settings') }}
                            </a>

                            <button v-if="plugin.instructions" class="button blue" @click="$refs['instructions-modal-'+i][0].open()">
                                {{ __('voyager::generic.instructions') }}
                            </button>
                            <modal v-if="plugin.instructions" :ref="'instructions-modal-'+i">
                                <div class="flex mb-4">
                                    <div class="w-2/3">
                                        <h4 class="text-gray-100 text-xl">{{ __('voyager::generic.instructions') }}</h4>
                                    </div>
                                    <div class="w-1/3 text-right text-gray-100">
                                        <button class="button green close-button">X</button>
                                    </div>
                                </div>
                                <div v-html="plugin.instructions"></div>
                            </modal>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    props: ['availablePlugins'],
    data: function () {
        return {
            installedPlugins: [],
            addPluginModalOpen: false,
            query: '',
            page: 0,
            loading: true,
            resultsPerPage: 5,
        };
    },
    methods: {
        closeAddPluginModal: function () {
            this.addPluginModalOpen = false;
        },
        copy: function (plugin) {
            this.copyToClipboard('composer require ' + plugin.repository);
            this.$snotify.info(this.__('voyager::plugins.copy_notice'));
        },
        loadPlugins: function () {
            var vm = this;
            vm.loading = true;
            axios.post(vm.route('voyager.plugins.get'), {
                _token: vm.$globals.csrf_token,
            })
            .then(function (response) {
                vm.installedPlugins = response.data;
            })
            .catch(function (errors) {
                // TODO: ...
            }).finally(function () {
                vm.loading = false;
            });
        },
        enablePlugin: function (plugin, enable) {
            var vm = this;
            var message = this.__('voyager::plugins.enable_plugin_confirm', {name: plugin.name});
            var title = this.__('voyager::plugins.enable_plugin');
            if (!enable) {
                message = this.__('voyager::plugins.disable_plugin_confirm', {name: plugin.name});
                title = this.__('voyager::plugins.disable_plugin');
            }

            vm.$snotify.confirm(message, title, {
                timeout: 5000,
                showProgressBar: true,
                closeOnClick: false,
                pauseOnHover: true,
                buttons: [
                    {
                        text: vm.__('voyager::generic.yes'),
                        action: (toast) => {
                            axios.post(vm.route('voyager.plugins.enable'), {
                                identifier: plugin.identifier,
                                enable: enable,
                                _token: vm.$globals.csrf_token,
                            })
                            .then(function (response) {
                                vm.$snotify.info(vm.__('voyager::plugins.reload_page'));
                            })
                            .catch(function (error) {
                                // TODO: This is not tested (error might be an array)
                                vm.$snotify.error(vm.__('voyager::plugins.error_changing_plugin') + ' ' + error.data);
                            }).finally(function () {
                                vm.loadPlugins();
                            });
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
        hasMultiplePlugins: function (type) {
            var num = 0;

            for (let plugin in this.installedPlugins) {
                if (plugin.enabled && plugin.type == 'type') {
                    num++;
                }
            }

            return num > 1;
        },
    },
    computed: {
        filteredPlugins: function () {
            var query = this.query.toLowerCase();
            return this.availablePlugins.filter(function (plugin) {
                return plugin.keywords.filter(function (keyword) {
                    return keyword.toLowerCase().indexOf(query) >= 0;
                }).length > 0;
            });
        },
        start: function () {
            return this.page * this.resultsPerPage;
        },
        end: function () {
            return this.start + this.resultsPerPage;
        },
        pages: function () {
            return Math.ceil(this.filteredPlugins.length / this.resultsPerPage);
        },
    },
    mounted: function () {
        this.loadPlugins();
    }
};
</script>