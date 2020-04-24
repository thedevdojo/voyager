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
        <div v-if="palette == 'tailwind-shades'" class="w-full text-center">
            <div class="flex w-full">
                <div v-for="(color, key) in $store.ui.colors" :key="'color-'+key" class="flex-1 mx-2">
                    <div v-for="(shade, i) in [100, 200, 300, 400, 500, 600, 700, 800, 900]" :key="'shade-'+i">
                        <button
                            class="button w-full my-2"
                            :class="`bg-${color}-${shade}`"
                            @click="$emit('select', color+'-'+shade)">
                            &nbsp;
                        </button>
                    </div>
                </div>
            </div>
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