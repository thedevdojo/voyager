<template>
    <component :is="tag">
        <slot />
    </component>
</template>
<script>
import { ElementMixin } from 'vue-slicksort';
export default {
    mixins: [ElementMixin],
    props: {
        tag: {
            type: String,
            default: 'div',
        },
    },
};
</script>
<style scoped>

</style>