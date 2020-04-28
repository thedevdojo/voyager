<template>
    <card :title="__('voyager::generic.breads')" icon="bread" :icon-size="8">
        <div slot="actions">
            <button class="button green" @click.stop="loadBreads">
                <icon icon="sync" class="rotating-ccw" :size="4" v-if="loading" />
                {{ __('voyager::builder.reload_breads') }}
            </button>
        </div>
        <div class="voyager-table striped" :class="[loading ? 'loading' : '']">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('voyager::generic.table') }}</th>
                        <th class="hidden md:table-cell">{{ __('voyager::generic.slug') }}</th>
                        <th class="hidden md:table-cell">{{ __('voyager::builder.name_singular') }}</th>
                        <th class="hidden md:table-cell">{{ __('voyager::builder.name_plural') }}</th>
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
                                    <icon icon="globe" :size="4" />
                                    <span>
                                        {{ __('voyager::generic.browse') }}
                                    </span>
                                </a>
                                <button class="button green" @click="backupBread(table)">
                                    <icon icon="history" :class="[backingUp ? 'rotating-ccw' : '']" :size="4" />
                                    <span v-if="getBackupsForTable(table).length > 0">
                                        {{ __('voyager::generic.backup') }} ({{ getBackupsForTable(table).length }})
                                    </span>
                                    <span v-else>
                                        {{ __('voyager::generic.backup') }}
                                    </span>
                                </button>
                                <dropdown ref="rollbackdd" v-if="getBackupsForTable(table).length > 0">
                                    <div>
                                        <a v-for="(bu, i) in getBackupsForTable(table)"
                                            :key="'rollback-'+i"
                                            href="#"
                                            @click.prevent="rollbackBread(table, bu)"
                                            class="link">
                                            {{ bu.date }}
                                        </a>
                                    </div>
                                    <div slot="opener">
                                        <button class="button green">
                                            <icon icon="clock" :size="4" />
                                            <span>
                                                {{ __('voyager::builder.rollback') }}
                                            </span>
                                        </button>
                                    </div>
                                </dropdown>
                                <a class="button yellow" :href="route('voyager.bread.edit', table)">
                                    <icon icon="pen" :size="4" />
                                    <span>
                                        {{ __('voyager::generic.edit') }}
                                    </span>
                                </a>
                                <button class="button red" @click="deleteBread(table)">
                                    <icon :icon="deleting ? 'sync' : 'trash'" :class="[deleting ? 'rotating-ccw' : '']" :size="4" />
                                    <span>
                                        {{ __('voyager::generic.delete') }}
                                    </span>
                                </button>
                            </div>
                            <a v-else class="button green" :href="route('voyager.bread.create', table)">
                                <icon icon="plus" :size="4" />
                                <span class="hidden md:block">
                                    {{ __('voyager::generic.add_type', { type: __('voyager::generic.bread') }) }}
                                    
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
            backups: [],
            loading: false,
            backingUp: false,
            deleting: false,
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
                vm.__('voyager::builder.delete_bread_confirm', {bread: table}),
                function (response) {
                    if (response) {
                        vm.deleting = true;
                        axios.delete(vm.route('voyager.bread.delete', table))
                        .then(function (response) {
                            vm.$notify.notify(vm.__('voyager::builder.delete_bread_success', {bread: table}), null, 'green', 5000);
                        })
                        .catch(function (errors) {
                            vm.$notify.notify(vm.__('voyager::builder.delete_bread_error', {bread: table}), null, 'red', 5000);
                        })
                        .then(function () {
                            vm.loadBreads();
                            vm.deleting = false;
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
            vm.backingUp = true;
            axios.post(vm.route('voyager.bread.backup-bread'), {
                table: table
            })
            .then(function (response) {
                vm.$notify.notify(vm.__('voyager::builder.bread_backed_up', { name: response.data }), null, 'blue', 5000);
            })
            .catch(function (error) {
                vm.$notify.notify(error.response.statusText, null, 'red', 5000);
            })
            .then(function () {
                vm.backingUp = false;
                vm.loadBreads();
            });
        },
        rollbackBread: function (table, backup) {
            var vm = this;
            vm.$refs.rollbackdd[0].close();
            axios.post(vm.route('voyager.bread.rollback-bread'), {
                table: table,
                path: backup.path
            })
            .then(function (response) {
                console.log(response);
                vm.$notify.notify(vm.__('voyager::builder.bread_rolled_back', { date: backup.date }), null, 'blue', 5000);
            })
            .catch(function (error) {
                vm.$notify.notify(error.response.statusText, null, 'red', 5000);
            })
            .then(function () {
                vm.loadBreads();
            });
        },
        getBackupsForTable: function (table) {
            return this.backups.filter(function (backup) {
                return backup.table == table;
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
                vm.breads = response.data.breads;
                vm.backups = response.data.backups;
            })
            .catch(function (error) {
                vm.$notify.notify(error.response.statusText, null, 'red', 5000);
            })
            .then(function () {
                vm.loading = false;
            });
        }
    },
    mounted: function () {
        this.loadBreads();
    }
};
</script>