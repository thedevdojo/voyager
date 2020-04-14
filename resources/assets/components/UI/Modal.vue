<template>
<div>
    <fade-transition>
        <div v-if="isOpened" class="modal inset-0 p-0 flex items-center justify-center z-40">
            <div v-if="isOpened" class="fixed inset-0 transition-opacity" @click="close">
                <div class="absolute inset-0 bg-black opacity-75"></div>
            </div>

            <div v-if="isOpened" class="body lg:w-3/4 xl:w-2/4">
                <card :title="title" :icon="icon" style="margin: 0 !important">
                    <div slot="actions">
                        <button style="margin: 0 !important" @click="close">
                            <icon icon="times"></icon>
                        </button>
                    </div>
                    <slot></slot>
                </card>
            </div>
        </div>
    </fade-transition>
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
        title: {
            type: String,
            default: ''
        },
        icon: {
            type: String,
            default: null
        },
        iconSize: {
            type: Number,
            default: 6
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
    @apply fixed w-full top-0 left-0 h-full z-40 text-white text-left overflow-y-hidden;

    .body {
        @apply z-50 rounded-lg absolute overflow-y-auto max-h-3/4;
    }
}
</style>