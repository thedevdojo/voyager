<template>
    <div>
        <locale-picker v-if="translatable" />
        <br>
        <h2>{{ __('voyager::bread.edit_name', {name: translate(bread.name_singular, true)}) }}</h2>
        <br>
        <div class="flex flex-wrap">
            <div v-for="(formfield, i) in layout.formfields" v-bind:key="'formfield-'+i" :class="'w-'+formfield.options.width">
                <component
                    :is="'formfield-'+formfield.type"
                    v-bind:data="getData(formfield.options.field)"
                    v-on:input="setData($event, formfield.options.field)"
                    :options="formfield.options"
                    :action="action" />
            </div>
        </div>
        <form method="post" :action="url" class="text-right">
            <input type="hidden" name="_token" :value="token">
            <input type="hidden" name="_method" :value="action == 'edit' ? 'PUT' : 'POST'">
            <input type="hidden" name="prev-url" :value="prevUrl">
            <input type="hidden" name="data" :value="jsonOutput">
            <button type="submit" name="" class="voyager-button blue">{{ __('voyager::bread.update_name', {name: translate(bread.name_singular, true)}) }}</button>
            <button type="submit" name="back" class="voyager-button blue">{{ __('voyager::bread.update_name_back', {name: translate(bread.name_singular, true)}) }}</button>
            <button type="submit" name="new" class="voyager-button blue">{{ __('voyager::bread.update_name_new', {name: translate(bread.name_singular, true)}) }}</button>
        </form>
    </div>
</template>

<script>
export default {
    props: ['bread', 'action', 'accessors', 'layout', 'input', 'translatable', 'url', 'prev-url'],
    data: function () {
        return {
            output: this.input,
        };
    },
    methods: {
        getData: function (field) {
            if (this.isFieldTranslatable(field)) {
                return translate(this.output[field]);
            }

            return this.output[field];
        },
        setData: function (value, field) {
            if (this.isFieldTranslatable(field)) {
                Vue.set(this.output[field], this.$eventHub.locale, value);
            } else {
                Vue.set(this.output, field, value);
            }
        },
        isFieldTranslatable: function (field) {
            return this.translatable.includes(field);
        }
    },
    computed: {
        token: function () {
            return document.head.querySelector('meta[name="csrf-token"]').content;
        },
        jsonOutput: function () {
            return JSON.stringify(this.output);
        }
    }
};
</script>