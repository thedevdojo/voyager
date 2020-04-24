<template>
    <div class="dropdown" v-click-outside="close">
        <slot name="opener" />
        <slide-y-up-transition>
            <div class="wrapper" :class="[`w-${width}`, pos]" v-if="show">
                <div class="body">
                    <slot />
                </div>
            </div>
        </slide-y-up-transition>
    </div>
</template>
<script>
export default {
    props: {
        pos: {
            type: String,
            default: 'left',
            validator: function (value) {
                return ['left', 'right'].indexOf(value) !== -1;
            }
        },
        width: {
            type: Number,
            default: 72,
        },
    },
    data: function () {
        return {
            show: false,
        };
    },
    methods: {
        open: function () {
            this.show = true;
        },
        close: function () {
            this.show = false;
        },
        toggle: function () {
            this.show = !this.show;
        },
    },
    mounted: function () {
        var vm = this;
        document.body.addEventListener('keydown', event => {
            if (event.keyCode === 27) {
                vm.close();
            }
        });
        if (vm.$slots.opener) {
            Array.from(vm.$slots.opener[0].elm.getElementsByTagName('button')).forEach(function (el) {
                el.addEventListener('click', event => {
                    vm.toggle();
                });
            });
        }
    },
};
</script>

<style lang="scss" scoped>
.dropdown {
    @apply relative inline-block text-left;

    .wrapper {
        @apply absolute mt-2 rounded-md shadow-lg border;

        &.left {
            @apply origin-top-left left-0;
        }

        &.right {
            @apply origin-top-right right-0;
        }

        .body {
            @apply rounded-md shadow-xs;
        }
    }
}
</style>