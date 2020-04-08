<template>
    <div class="card">
        <div class="flex w-full">
            <div class="w-1/2">
                <h2 class="title">{{ __('voyager::generic.breads') }}</h2>
            </div>
            <div class="w-1/2 text-right">
                <button class="button green" @click.stop="loadBreads">
                    <icon icon="sync" class="mr-0 md:mr-1" :class="[loading ? 'rotating-ccw' : '']" />
                    <span class="hidden md:block">Reload BREADs</span>
                </button>
            </div>
        </div>
        <div class="body">
            <div class="voyager-table striped">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('voyager::generic.table') }}</th>
                            <th>{{ __('voyager::generic.slug') }}</th>
                            <th>{{ __('voyager::manager.name_singular') }}</th>
                            <th>{{ __('voyager::manager.name_plural') }}</th>
                            <th style="text-align:right !important">{{ __('voyager::generic.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="table in tables" v-bind:key="table">
                            <td>{{ table }}</td>
                            <td>
                                <span v-if="hasBread(table)">{{ translate(getBread(table).slug) }}</span>
                            </td>
                            <td>
                                <span v-if="hasBread(table)">{{ translate(getBread(table).name_singular) }}</span>
                            </td>
                            <td>
                                <span v-if="hasBread(table)">{{ translate(getBread(table).name_plural) }}</span>
                            </td>
                            <td class="text-right">
                                <div v-if="hasBread(table)">
                                    <a class="button blue" :href="route('voyager.'+table+'.browse')">
                                        <icon icon="globe" class="mr-0 md:mr-1" />
                                        <span class="hidden md:block">Browse</span>
                                    </a>
                                    <button class="button green" @click="backupBread(table)">
                                        <icon icon="history" class="mr-0 md:mr-1" />
                                        <span class="hidden md:block">Backup</span>
                                    </button>
                                    <a class="button yellow" :href="route('voyager.bread.edit', table)">
                                        <icon icon="pen" class="mr-0 md:mr-1" />
                                        <span class="hidden md:block">Edit</span>
                                    </a>
                                    <button class="button red" @click="deleteBread(table)">
                                        <icon icon="trash" class="mr-0 md:mr-1" />
                                        <span class="hidden md:block">Delete</span>
                                    </button>
                                </div>
                                <a v-else class="button green" :href="route('voyager.bread.create', table)">
                                    <icon icon="plus" class="mr-0 md:mr-1" />
                                    <span class="hidden md:block">Add BREAD</span>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
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
                vm.__('voyager::generic.yes'),
                vm.__('voyager::generic.no'),
                7500
            ).then(function (response) {
                if (response) {
                    axios.delete(vm.route('voyager.bread.delete', table))
                    .then(function (response) {
                        vm.$notify.success(vm.__('voyager::manager.delete_bread_success', {bread: table}));
                    })
                    .catch(function (errors) {
                        vm.$notify.error(vm.__('voyager::manager.delete_bread_error', {bread: table}));
                    })
                    .then(function () {
                        vm.loadBreads();
                    });
                }
            }).catch(function () {
                // Promise rejected means "closed" (manually or after timeout). Do nothing
            });
        },
        backupBread: function (table) {
            var vm = this;
            axios.post(vm.route('voyager.bread.backup-bread'), {
                table: table
            })
            .then(function (response) {
                vm.$notify.success('BREAD successfully backed-up');
            })
            .catch(function (error) {
                vm.$notify.error(error.response.statusText, {
                    timeout: 2500
                });
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
                vm.$notify.error(error.response.statusText, {
                    timeout: 2500
                });
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