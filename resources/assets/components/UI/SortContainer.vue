<template>
    <component :is="tag">
        <slot />
    </component>
</template>
<script>
import { ContainerMixin } from 'vue-slicksort';

export default {
    mixins: [ContainerMixin],
    props: {
        tag: {
            type: String,
            default: 'div'
        },
    },
};
</script>
<style scoped>

</style>