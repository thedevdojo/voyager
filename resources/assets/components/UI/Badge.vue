<template>
    <div
        class="inline-block text-white text-sm font-semibold px-2 rounded-full tracking-wide mr-1 cursor-pointer"
        :class="`bg-${color}-500 text-gray-100 hover:bg-${color}-600 hover:text-white`">
        <slot></slot>
    </div>
</template>
<script>
export default {
    props: {
        color: {
            type: String,
            default: 'green'
        },
    },
};
</script>