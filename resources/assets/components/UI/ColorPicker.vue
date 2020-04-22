<template>
    <div>
        <div v-if="palette == 'tailwind-colors'" class="w-full text-center">
            <button
                v-for="(color, key) in $store.ui.colors" :key="'color-'+key"
                @click="$emit('select', color)"
                class="button" :class="color">
                {{ ucfirst(color) }}
            </button>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        palette: {
            type: String,
            default: 'tailwind-colors',
            validator: function (value) {
                return ['tailwind-colors', 'tailwind-shades'].indexOf(value) !== -1;
            }
        },
    },
};
</script>

<style scoped>

</style>