<template>
    <table class="voyager-table striped">
        <thead>
            <tr>
                <th>{{ __('voyager::generic.table') }}</th>
                <th class="text-right">{{ __('voyager::generic.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="table in tables" v-bind:key="table">
                <td>{{ table }}</td>
                <td class="text-right">
                    <div v-if="hasBread(table)">
                        <a class="button blue" :href="route('voyager.'+table+'.browse')">
                            {{ __('voyager::generic.browse') }}
                        </a>
                        <a class="button yellow" :href="route('voyager.bread.edit', table)">
                            {{ __('voyager::generic.edit_type', {type: __('voyager::bread.bread')}) }}
                        </a>
                        <button class="button red" @click="deleteBread(table)">
                            {{ __('voyager::generic.delete_type', {type: __('voyager::bread.bread')}) }}
                        </button>
                    </div>
                    <a v-else class="button green" :href="route('voyager.bread.create', table)">
                        {{ __('voyager::generic.add_type', {type: __('voyager::bread.bread')}) }}
                    </a>
                </td>
            </tr>
        </tbody>
    </table>
</template>

<script>
export default {
    props: ['tables', 'breads'],
    data: function () {
        return {
            
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
            vm.$snotify.confirm(vm.__('voyager::manager.delete_bread_confirm', {bread: table}), vm.__('voyager::manager.delete_bread'), {
                timeout: 5000,
                showProgressBar: true,
                closeOnClick: false,
                pauseOnHover: true,
                buttons: [
                    {
                        text: vm.__('voyager::generic.yes'),
                        action: (toast) => {
                            axios.delete(this.route('voyager.bread.delete', table), {
                                _token: vm.$globals.csrf_token,
                            })
                            .then(function (response) {
                                vm.$snotify.success(vm.__('voyager::manager.delete_bread_success', {bread: table}));
                            })
                            .catch(function (errors) {
                                vm.$snotify.error(vm.__('voyager::manager.delete_bread_error', {bread: table}));
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
        }
    },
};
</script>