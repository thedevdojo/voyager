<template>
    <div>
        <div v-bind:class="['slidein', isOpened ? 'open' : 'close']" v-click-outside="closeOutside">
            <slot />
        </div>
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
            justOpened: true,
        };
    },
    methods: {
        open: function () {
            this.isOpened = true;
        },
        close: function () {
            this.isOpened = false;
        },
        closeOutside: function () {
            if (this.justOpened) {
                this.justOpened = false;
            } else {
                this.isOpened = false;
            }
        },
        toggle: function () {
            this.isOpened = !this.isOpened;
        }
    },
    watch: {
        opened: function (opened) {
            this.isOpened = opened;
            if (opened) {
                this.justOpened = true;
            }
        },
        isOpened: function (opened) {
            if (opened) {
                this.$emit('opened');
            } else {
                this.$emit('closed');
            }
        }
    }
};
</script>

<style lang="scss" scoped>
.slidein {
    @apply fixed w-1/4 top-0 left-auto right-0 h-full overflow-y-hidden;
    background-color: rgba(0, 0, 0, .5);
    transition: opacity 0.3s ease;
    z-index: 100;

    &.open {
        @apply block;
    }
    &.close {
        @apply hidden;
    }
}
</style>