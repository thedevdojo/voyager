<template>
    <card :title="__('voyager::generic.breads')" icon="bread" :icon-size="8">
        <div slot="actions">
            <button class="button green" @click.stop="loadBreads">
                <icon icon="sync" :class="[loading ? 'rotating-ccw' : '']" />
                <span>{{ __('voyager::manager.reload_breads') }}</span>
            </button>
        </div>
        <div class="voyager-table striped" :class="[loading ? 'loading' : '']">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('voyager::generic.table') }}</th>
                        <th class="hidden md:table-cell">{{ __('voyager::generic.slug') }}</th>
                        <th class="hidden md:table-cell">{{ __('voyager::manager.name_singular') }}</th>
                        <th class="hidden md:table-cell">{{ __('voyager::manager.name_plural') }}</th>
                        <th style="text-align:right !important">{{ __('voyager::generic.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="table in tables" v-bind:key="table">
                        <td>{{ table }}</td>
                        <td class="hidden md:table-cell">
                            <span v-if="hasBread(table)">{{ translate(getBread(table).slug) }}</span>
                        </td>
                        <td class="hidden md:table-cell">
                            <span v-if="hasBread(table)">{{ translate(getBread(table).name_singular) }}</span>
                        </td>
                        <td class="hidden md:table-cell">
                            <span v-if="hasBread(table)">{{ translate(getBread(table).name_plural) }}</span>
                        </td>
                        <td class="text-right">
                            <div v-if="hasBread(table)">
                                <a class="button blue" :href="route('voyager.'+table+'.browse')">
                                    <icon icon="globe" />
                                    <span>
                                        {{ __('voyager::generic.browse') }}
                                    </span>
                                </a>
                                <button class="button green" @click="backupBread(table)">
                                    <icon icon="history" />
                                    <span>
                                        {{ __('voyager::generic.backup') }}
                                    </span>
                                </button>
                                <a class="button yellow" :href="route('voyager.bread.edit', table)">
                                    <icon icon="pen" />
                                    <span>
                                        {{ __('voyager::generic.edit') }}
                                    </span>
                                </a>
                                <button class="button red" @click="deleteBread(table)">
                                    <icon icon="trash" />
                                    <span>
                                        {{ __('voyager::generic.delete') }}
                                    </span>
                                </button>
                            </div>
                            <a v-else class="button green" :href="route('voyager.bread.create', table)">
                                <icon icon="plus" />
                                <span class="hidden md:block">
                                    {{ __('voyager::manager.add_bread') }}
                                </span>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </card>
</template>

<script>
export default {
    props: ['tables'],
    data: function () {
        return {
            breads: [],
            loading: false,
        };
    },
    methods: {
        hasBread: function (table) {
            return this.getBread(table) !== null;
        },
        getBread: function (table) {
            var bread = null;
            this.breads.forEach(b => {
                if (b.table == table) {
                    bread = b;
                }
            });

            return bread;
        },
        deleteBread: function (table) {
            var vm = this;

            vm.$notify.confirm(
                vm.__('voyager::manager.delete_bread_confirm', {bread: table}),
                function (response) {
                    if (response) {
                        axios.delete(vm.route('voyager.bread.delete', table))
                        .then(function (response) {
                            vm.$notify.notify(vm.__('voyager::manager.delete_bread_success', {bread: table}), null, 'green', 5000);
                        })
                        .catch(function (errors) {
                            vm.$notify.notify(vm.__('voyager::manager.delete_bread_error', {bread: table}), null, 'red', 5000);
                        })
                        .then(function () {
                            vm.loadBreads();
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
        backupBread: function (table) {
            var vm = this;
            axios.post(vm.route('voyager.bread.backup-bread'), {
                table: table
            })
            .then(function (response) {
                vm.$notify.notify(vm.__('voyager::manager.bread_backed_up'), null, 'blue', 5000);
            })
            .catch(function (error) {
                vm.$notify.notify(error.response.statusText, null, 'red', 5000);
            });
        },
        loadBreads: function () {
            var vm = this;

            if (vm.loading) {
                return;
            }

            vm.loading = true;
            axios.post(vm.route('voyager.bread.get-breads'))
            .then(function (response) {
                vm.breads = response.data;
            })
            .catch(function (error) {
                vm.$notify.notify(error.response.statusText, null, 'red', 5000);
            })
            .then(function () {
                vm.loading = false;
            });
        },
    },
    mounted: function () {
        this.loadBreads();
    }
};
</script>