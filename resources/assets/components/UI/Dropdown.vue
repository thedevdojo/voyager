<template>
    <div>
        <div @click="toggle">
            <slot slot="opener">{{ opened ? 'Close' : 'Open' }}</slot>
        </div>
        <div v-if="opened">
            <slot />
        </div>
    </div>
</template>
<script>
export default {
    data: function () {
        return {
            opened: false,
        };
    },
    methods: {
        open: function () {
            this.opened = true;
        },
        close: function () {
            this.opened = false;
        },
        toggle: function () {
            this.opened = !this.opened;
        }
    },
    mounted: function () {
        var vm = this;
        document.body.addEventListener('keydown', event => {
            if (event.keyCode === 27) {
                vm.close();
            }
        });
    }
};
</script>