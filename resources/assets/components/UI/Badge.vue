<template>
    <span
        class="badge"
        :class="`bg-${color}-400 text-${color}-800 hover:bg-${color}-500`">
        <slot></slot>
    </span>
</template>
<script>
export default {
    props: {
        color: {
            type: String,
            default: 'green'
        }
    },
};
</script>

<style lang="scss" scoped>
.badge {
    @apply inline-flex items-center px-2 py-1 rounded-full text-xs font-medium leading-4 m-1 cursor-default transition ease-in-out duration-150;

    &.large {
        @apply px-3 text-sm leading-5;
    }
}
</style>