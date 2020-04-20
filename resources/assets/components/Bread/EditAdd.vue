<template>
    <card :title="__('voyager::generic.add_type', { type: translate(bread.name_singular) })" :icon="bread.icon">
        <div slot="actions">
                <div class="flex items-center">
                    <a class="button blue" v-if="prevUrl !== ''" :href="prevUrl">
                        <icon icon="backward"></icon>
                        <span>{{ __('voyager::generic.back') }}</span>
                    </a>
                </div>
            </div>
            <div class="flex flex-wrap w-full">
                <div v-for="(formfield, key) in layout.formfields" :key="'formfield-'+key" class="m-0" :class="formfield.options.width">
                    <card :show-header="false">
                        <component
                            :is="'formfield-'+formfield.type+'-edit-add'"
                            v-bind:value="data(formfield, null)"
                            v-on:input="data(formfield, $event)"
                            :options="formfield.options"
                            :show="action" />
                    </card>
                </div>
            </div>
    </card>
</template>

<script>
export default {
    props: ['bread', 'action', 'input', 'layout', 'prevUrl'],
    data: function () {
        return {
            
        };
    },
    methods: {
        data: function (formfield, value = null) {
            if (value) {
                //if (formfield.options.translatable || false) {
                    //Vue.set(this.output[formfield.column], this.$language.locale, value);
                //} else {
                    Vue.set(this.output, formfield.column.column, value);
                //}
                //this.$globals.$emit('formfield-input', formfield.column, value, formfield.options.translatable);
            }
            //if (formfield.options.translatable) {
                //var translated = this.translate(this.output[formfield.column]);
                //return translated;
            //}
            return this.input[formfield.column.column];
        },
    },
};
</script>