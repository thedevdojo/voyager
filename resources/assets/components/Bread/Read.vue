<template>
    <div>
        <card :title="__('voyager::bread.read_type', { type: translate(bread.name_singular, true) })" :icon="bread.icon">
            <div slot="actions">
                <div class="flex items-center">
                    <a class="button blue" v-if="prevUrl !== ''" :href="prevUrl">
                        <icon icon="backward"></icon>
                        <span>{{ __('voyager::generic.back') }}</span>
                    </a>
                    <a class="button yellow" :href="route('voyager.'+translate(bread.slug, true)+'.edit', primary)">
                        <icon icon="pen"></icon>
                        <span>{{ __('voyager::generic.edit') }}</span>
                    </a>
                    <locale-picker v-if="$language.localePicker" :small="false"></locale-picker>
                </div>
            </div>
            <div class="flex flex-wrap w-full">
                <div v-for="(formfield, key) in layout.formfields" :key="'formfield-'+key" class="m-0" :class="formfield.options.width">
                    <card :show-header="false">
                        <component
                            :is="'formfield-'+formfield.type+'-read'"
                            :data="getData(formfield.column)"
                            :translatable="formfield.translatable"
                            :options="formfield.options" />
                    </card>
                </div>
            </div>
        </card>
    </div>
</template>

<script>
export default {
    props: ['bread', 'data', 'primary', 'layout', 'prevUrl', 'translatable'],
    methods: {
        getData: function (column) {
            return this.data[column.column];
        }
    },
    mounted: function () {
        if (this.translatable) {
            Vue.prototype.$language.localePicker = true;
        }
    }
};
</script>