<template>
    <span
        class="badge"
        :class="`bg-${color}-400 text-${color}-800 hover:bg-${color}-500`">
        <svg class="ltr:mr-1 rtl:ml-1 h-2 w-2" :class="`text-${color}-700`" fill="currentColor" viewBox="0 0 8 8" v-if="dot">
            <circle cx="4" cy="4" r="3" />
        </svg>
        <slot></slot>
        <icon
            v-on:click.native="$emit('click-icon', $event)"
            class="ltr:ml-1 rtl:mr-1 cursor-pointer"
            :size="5"
            v-if="icon !== ''"
            :icon="icon">
        </icon>
    </span>
</template>
<script>
export default {
    props: {
        color: {
            type: String,
            default: 'green',
        },
        dot: {
            type: Boolean,
            default: false,
        },
        icon: {
            type: String,
            default: '',
        }
    },
};
</script>

<style lang="scss" scoped>
.badge {
    @apply inline-flex items-center px-2 py-1 rounded-full text-xs font-medium leading-4 m-1 transition ease-in-out duration-150;

    &.large {
        @apply px-3 text-sm leading-5;
    }
}
</style>