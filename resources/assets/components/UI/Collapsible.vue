<template>
<card :title="title" header-pointer v-on:click-header="toggle">
    <div slot="actions">
        <icon :icon="open ? 'angle-up' : 'angle-down'" :size="8"></icon>
    </div>
    <collapse-transition>
        <div v-show="open">
            <slot></slot>
        </div>
    </collapse-transition>
</card>
</template>
<script>
export default {
    props: {
        title: {
            type: String,
            default: '',
        },
        opened: {
            type:Boolean,
            default: true,
        }
    },
    data: function () {
        return {
            open: this.opened,
        };
    },
    methods: {
        toggle: function () {
            this.open = !this.open;

            if (this.open) {
                this.$emit('open');
            } else {
                this.$emit('close');
            }

            this.$emit('toggle');
        }
    }
};
</script>

<style lang="scss" scoped>

</style>