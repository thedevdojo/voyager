<template>
    <div>
        <transition name="fade">
            <div v-bind:class="['modal', isOpened ? 'open' : 'close']" @click="click">
                <div class="modal-body">
                    <slot />
                </div>
            </div>
        </transition>
        <slot name="opener"></slot>
    </div>
</template>
<script>
export default {
    props: {
        opened: {
            type: Boolean,
            default: false
        },
    },
    data: function () {
        return {
            isOpened: this.opened,
        };
    },
    methods: {
        open: function () {
            this.isOpened = true;
        },
        close: function () {
            this.isOpened = false;
        },
        toggle: function () {
            this.isOpened = !this.isOpened;
        },
        click: function (e) {
            if (e.target.classList.contains('modal')) {
                this.toggle();
            }
        }
    },
    watch: {
        opened: function (opened) {
            this.isOpened = opened;
        },
        isOpened: function (opened) {
            if (opened) {
                this.$emit('opened');
            } else {
                this.$emit('closed');
            }
        }
    },
    mounted: function () {
        var vm = this;
        document.body.addEventListener('keydown', event => {
            if (event.keyCode === 27) {
                vm.close();
            }
        });
        Array.from(vm.$el.getElementsByClassName('close-button')).forEach(function (el) {
            el.addEventListener('click', event => {
                vm.close();
            });
        });
        if (vm.$slots.opener) {
            // TODO: We might need to check for other element aswell
            Array.from(vm.$slots.opener[0].elm.getElementsByTagName('button')).forEach(function (el) {
                el.addEventListener('click', event => {
                    vm.open();
                });
            });
        }
    },
};
</script>

<style lang="scss" scoped>
.modal {
    @apply fixed w-full top-0 left-0 h-full z-40 text-white text-left;
    background-color: rgba(0, 0, 0, .7);

    &.open {
        @apply visible opacity-100;
    }
    &.close {
        @apply invisible opacity-0;
    }

    .modal-body {
        @apply bg-gray-700 border-4 border-gray-700 rounded-lg absolute p-8 overflow-y-auto;
        max-height: 90%;
        left: 25%;
        width: 50%;
        top: 50%;
        transform: translateY(-50%);
    }
}
</style>