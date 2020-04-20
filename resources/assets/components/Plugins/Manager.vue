<template>
    <card :title="__('voyager::plugins.plugins')" icon="puzzle-piece">
        <div slot="actions">
            <modal ref="search_plugin_modal" :title="__('voyager::plugins.plugins')" icon="puzzle-piece">
                <input type="text" class="voyager-input w-full mb-3" v-model="query" :placeholder="__('voyager::generic.search')">
                <div v-for="(plugin, i) in filteredPlugins.slice(start, end)" :key="'plugin-'+i">
                    <div class="flex">
                        <div class="w-3/5">
                            <div class="inline-flex">
                                <h5 class="mr-2">{{ plugin.name }}</h5>
                                <badge :color="getPluginTypeColor(plugin.type)">{{ __('voyager::plugins.types.'+plugin.type) }}</badge>
                            </div>
                            <p>{{ plugin.description }}</p>
                            <a v-if="plugin.website" :href="plugin.website" target="_blank">
                                {{ __('voyager::generic.website') }}
                            </a>
                            <span v-else>&nbsp;</span>
                        </div>
                        <div class="w-2/5 text-right">
                            <input class="voyager-input w-full select-none" :value="'composer require '+plugin.repository" @dblclick="copy(plugin)">
                        </div>
                    </div>
                    <hr class="w-full bg-gray-300 my-4">
                </div>
                <div class="w-full text-right">
                    <div class="button-group">
                        <button class="button blue" v-for="i in pages" @click="page = (i - 1)" :key="'page-button-'+i">{{ i }}</button>
                    </div>
                </div>
                <div slot="opener" class="">
                    <button class="button green">
                        <icon icon="search"></icon>
                        {{ __('voyager::plugins.search_plugins') }}
                    </button>
                </div>
            </modal>
        </div>
        <alert color="red" v-if="hasMultiplePlugins('auth')" class="mb-2" v-html="nl2br(__('voyager::plugins.multiple_auth_plugins'))"></alert>
        <alert color="red" v-if="hasMultiplePlugins('menu')" class="mb-2" v-html="nl2br(__('voyager::plugins.multiple_menu_plugins'))"></alert>

        <div class="voyager-table striped" v-if="installedPlugins.length > 0" v-bind:class="[loading ? 'loading' : '']">
            <table id="bread-manager-browse">
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
                        <th class="ltr:text-right rtl:text-left">
                            {{ __('voyager::generic.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(plugin, i) in installedPlugins" :key="'installed-plugin-'+i">
                        <td>{{ translate(plugin.name) }}</td>
                        <td>{{ translate(plugin.description) }}</td>
                        <td>
                            <span class="badge" :class="getPluginTypeColor(plugin.type)">
                                {{ __('voyager::plugins.types.'+plugin.type) }}
                            </span>
                        </td>
                        <td>
                            {{ plugin.version || '-' }}
                        </td>
                        <td class="ltr:text-right rtl:text-left">
                            <a class="button green small" v-if="plugin.website" :href="plugin.website" target="_blank">
                                <icon icon="globe"></icon>
                                {{ __('voyager::generic.website') }}
                            </a>
                            <button v-if="!plugin.enabled" class="button green small" @click="enablePlugin(plugin, true)">
                                <icon icon="toggle-on"></icon>
                                {{ __('voyager::generic.enable') }}
                            </button>
                            <button v-else class="button red small" @click="enablePlugin(plugin, false)">
                                <icon icon="toggle-off"></icon>
                                {{ __('voyager::generic.disable') }}
                            </button>
                            <a v-if="plugin.has_settings && plugin.enabled" :href="route('voyager.plugins.settings', i)" class="button blue small">
                                <icon icon="cog"></icon>
                                {{ __('voyager::generic.settings') }}
                            </a>

                            <button v-if="plugin.instructions" class="button blue small" @click="$refs['instructions-modal-'+i][0].open()">
                                <icon icon="map-marker-question"></icon>
                                {{ __('voyager::generic.instructions') }}
                            </button>
                            <modal v-if="plugin.instructions" :ref="'instructions-modal-'+i">
                                <div class="flex mb-4">
                                    <div class="w-2/3">
                                        <h4 class="text-gray-100 text-xl">{{ __('voyager::generic.instructions') }}</h4>
                                    </div>
                                    <div class="w-1/3 text-right text-gray-100">
                                        <icon icon="times"></icon>
                                    </div>
                                </div>
                                <div v-html="plugin.instructions"></div>
                            </modal>
                            <button v-if="plugin.type == 'theme' && !plugin.enabled" class="button purple small" @click="previewTheme(plugin.src, plugin.name)">
                                <icon icon="eye"></icon>
                                {{ __('voyager::generic.preview') }}
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else class="w-full text-center">
            <h3>No plugins installed 😞</h3>
            <h4>Go ahead and install one</h4>
        </div>
    </card>
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
            this.$notify.notify(this.__('voyager::plugins.copy_notice'), null, 'blue', 5000);
        },
        loadPlugins: function () {
            var vm = this;
            vm.loading = true;
            axios.post(vm.route('voyager.plugins.get'), {
                _token: vm.csrf_token,
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
            if (!enable) {
                message = this.__('voyager::plugins.disable_plugin_confirm', {name: plugin.name});
            }

            vm.$notify.confirm(
                message,
                function (response) {
                    if (response) {
                        axios.post(vm.route('voyager.plugins.enable'), {
                            identifier: plugin.identifier,
                            enable: enable,
                        })
                        .then(function (response) {
                            vm.$notify.notify(vm.__('voyager::plugins.reload_page'));
                        })
                        .catch(function (error) {
                            // TODO: This is not tested (error might be an array)
                            vm.$notify.notify(vm.__('voyager::plugins.error_changing_plugin') + ' ' + error.data);
                        }).finally(function () {
                            vm.loadPlugins();
                        });
                    }
                },
                false,
                'blue',
                vm.__('voyager::generic.yes'),
                vm.__('voyager::generic.no'),
                7500
            );
        },
        previewTheme: function (src, name) {
            var file = document.createElement('link');
            file.setAttribute('rel', 'stylesheet');
            file.setAttribute('type', 'text/css');
            file.setAttribute('href', src);
            document.getElementsByTagName('head')[0].appendChild(file);

            this.$notify.notify(this.__('voyager::plugins.preview_theme', {name: name}), null, 'blue', 5000);
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
        getPluginTypeColor: function (type) {
            if (type == 'authentication') {
                return 'green';
            } else if (type == 'authorization') {
                return 'blue';
            } else if (type == 'menu') {
                return 'yellow';
            } else if (type == 'theme') {
                return 'purple';
            } else if (type == 'widget') {
                return 'orange';
            }

            return 'red';
        }
    },
    computed: {
        filteredPlugins: function () {
            var query = this.query.toLowerCase();
            return this.availablePlugins.filter(function (plugin) {
                if (plugin.type == query) {
                    return true;
                }
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

        var type = this.getParameterFromUrl('type', null);
        if (type !== null) {
            this.query = type[0].toUpperCase() + type.slice(1);
            this.$refs.search_plugin_modal.open();
        }
    }
};
</script>