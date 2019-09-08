<template>
    <table class="text-left m-4 w-full" style="border-collapse:collapse" id="bread-manager-browse">
        <thead>
            <tr>
                <th class="py-4 px-6 bg-grey-lighter font-sans font-medium uppercase text-sm text-grey border-b border-grey-light">
                    {{ __('voyager::generic.table') }}
                </th>
                <th class="py-4 px-6 bg-grey-lighter font-sans font-medium uppercase text-sm text-grey border-b border-grey-light text-right">
                    {{ __('voyager::generic.actions') }}
                </th>
            </tr>
        </thead>
        <tbody>
            <tr class="hover:bg-blue-lightest" v-for="table in tables" v-bind:key="table">
                <td class="py-4 px-6 border-b border-grey-light">{{ table }}</td>
                <td class="py-4 px-6 border-b border-grey-light text-right">
                    <div v-if="hasBread(table)">
                        <a class="voyager-button blue" :dusk="'edit-'+table" :href="route('voyager.bread.edit', table)">
                            {{ __('voyager::generic.edit_type', {type: __('voyager::bread.bread')}) }}
                        </a>
                        <button class="voyager-button red" :dusk="'delete-'+table" @click="deleteBread(table)">
                            {{ __('voyager::generic.delete_type', {type: __('voyager::bread.bread')}) }}
                        </button>
                    </div>
                    <a v-else class="voyager-button green" :dusk="'add-'+table" :href="route('voyager.bread.create', table)">
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
                            axios.delete(route('voyager.bread.delete', table), {
                                _token: document.head.querySelector('meta[name="csrf-token"]').content,
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