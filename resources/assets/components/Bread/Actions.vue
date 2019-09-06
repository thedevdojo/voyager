<template>
    <div class="inline-flex">
        <component
            v-for="(action, i) in (mass ? massActions: normalActions)"
            :key="'action-'+i"
            :is="action.method == 'get' ? 'a' : 'button'"
            :href="action.method == 'get' ? getUrl(action) : false"
            :class="action.classes"
            @click="click(action)"
            >
            {{ getTitle(action.title) }}
        </component>
    </div>
</template>

<script>
export default {
    props: ['actions', 'keys', 'bread', 'mass'],
    data: function () {
        return {
            
        };
    },
    methods: {
        getTitle: function (str) {
            var length = 1;
            if (typeof this.keys === 'object') {
                length = this.keys.length
            }
            return str
                .replace(':num:', length)
                .replace(':display_name:', this.getDisplayName());
        },
        getDisplayName: function () {
            var length = 1;
            if (typeof this.keys === 'object') {
                length = this.keys.length
            }
            if (length == 1) {
                return this.translate(this.bread.name_singular);
            }

            return this.translate(this.bread.name_plural);
        },
        getUrl: function (action) {
            return action.url.replace(':key:', this.keys);
        },
        click: function (action) {
            var vm = this;
            if (action.method == 'get') {
                return true;
            }
            if (action.confirm) {
                vm.$snotify.confirm(vm.getTitle(action.confirm.text), vm.getTitle(action.confirm.title), {
                    timeout: 5000,
                    showProgressBar: true,
                    closeOnClick: false,
                    pauseOnHover: true,
                    buttons: [
                        {
                            text: action.confirm.yes,
                            action: (toast) => {
                                vm.$snotify.remove(toast.id);
                                vm.callAction(action);
                            }
                        },
                        {
                            text: action.confirm.no,
                            action: (toast) => {
                                vm.$snotify.remove(toast.id);
                            }
                        },
                    ]
                });
            } else {
                callAction(action);
            }
        },
        callAction: function (action) {
            var vm = this;
            console.log(vm.getUrl(action));
            axios({
                method: action.method,
                url: vm.getUrl(action),
                data: {
                    keys: vm.keys,
                    _token: document.head.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(function (response) {
                if (response.data.success) {
                    vm.$snotify.success(response.data.message);
                } else {
                    vm.$snotify.error(response.data.message);
                }
                vm.$parent.loadItems();
            })
            .catch(function (error) {
                vm.$snotify.error(error);
            });
        }
    },
    computed: {
        massActions: function () {
            var vm = this;
            return this.actions.filter(function (action) {
                return action.massAction && vm.keys.length > 0;
            });
        },
        normalActions: function () {
            return this.actions.filter(function (action) {
                return !action.massAction;
            });
        }
    }
};
</script>