<template>
    <div>
        <h2 class="text-3xl mb-5" v-if="action == 'add'">{{ __('voyager::generic.add_type', {type: translate(bread.name_singular, true)}) }}</h2>
        <h2 class="text-3xl mb-5" v-else>{{ __('voyager::bread.edit_name', {name: translate(bread.name_singular, true)}) }}</h2>
        <div class="flex">
            <a class="button green small" :href="route('voyager.'+translate(bread.slug, true)+'.create')" v-if="action == 'edit'">
                {{ __('voyager::generic.add_type', {type: translate(bread.name_singular, true)}) }}
            </a>
            <a class="button blue small" :href="route('voyager.'+translate(bread.slug, true)+'.index')">
                {{ __('voyager::bread.browse_name', {name: translate(bread.name_plural, true)}) }}
            </a>
            <a
                class="button yellow small"
                :href="route('voyager.bread.edit', bread.table)"
                v-if="debug">
                {{ __('voyager::bread.edit_name', {name: __('voyager::bread.bread')}) }}
            </a>
            <a
                class="button yellow small"
                :href="route('voyager.bread.edit', bread.table)+'?layout='+layoutId"
                v-if="debug">
                {{ __('voyager::bread.edit_name', {name: __('voyager::manager.layout')}) }}
            </a>
        </div>
        <br>
        <div class="flex flex-wrap">
            <div v-for="(formfield, i) in layout.formfields" v-bind:key="'formfield-'+i" :class="'w-'+formfield.options.width+' voyager-card'">
                <div class="body">
                    <component
                        :is="'formfield-'+formfield.type"
                        v-bind:value="data(formfield.field, null)"
                        v-on:input="data(formfield.field, $event)"
                        :options="formfield.options"
                        :action="action" />
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert" v-if="getErrors(formfield.field).length > 0">
                        <p v-for="(error, key) in getErrors(formfield.field)" :key="'error-'+key">
                            {{ error }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <form method="post" :action="url">
            <input type="hidden" name="_token" :value="token">
            <input type="hidden" name="_method" :value="action == 'edit' ? 'PUT' : 'POST'">
            <input type="hidden" name="prev-url" :value="prevUrl">
            <input type="hidden" name="data" :value="jsonOutput">
            <div v-if="action == 'add'" class="button-group">
                <button type="submit" name="_redirect" value="" class="button blue">{{ __('voyager::bread.store_name', {name: translate(bread.name_singular, true)}) }}</button>
                <button type="submit" name="_redirect" value="back" class="button blue">{{ __('voyager::bread.store_name_back', {name: translate(bread.name_singular, true)}) }}</button>
                <button type="submit" name="_redirect" value="new" class="button blue">{{ __('voyager::bread.store_name_new', {name: translate(bread.name_singular, true)}) }}</button>
            </div>
            <div v-else class="button-group">
                <button type="submit" name="_redirect" value="" class="button blue">{{ __('voyager::bread.update_name', {name: translate(bread.name_singular, true)}) }}</button>
                <button type="submit" name="_redirect" value="back" class="button blue">{{ __('voyager::bread.update_name_back', {name: translate(bread.name_singular, true)}) }}</button>
                <button type="submit" name="_redirect" value="new" class="button blue">{{ __('voyager::bread.update_name_new', {name: translate(bread.name_singular, true)}) }}</button>
            </div>
        </form>
    </div>
</template>

<script>
export default {
    props: ['bread', 'action', 'errors', 'accessors', 'layout', 'input', 'translatable', 'url', 'prev-url'],
    data: function () {
        return {
            output: this.input,
        };
    },
    methods: {
        isFieldTranslatable: function (field) {
            return this.translatable.includes(field);
        },
        data: function (field, value = null) {
            if (value) {
                if (this.isFieldTranslatable(field)) {
                    Vue.set(this.output[field], this.$language.locale, value);
                } else {
                    Vue.set(this.output, field, value);
                }
                this.$globals.$emit('formfield-input', field, value, this.isFieldTranslatable(field));
            }

            if (this.isFieldTranslatable(field)) {
                var translated = this.translate(this.output[field]);

                return translated;
            }

            return this.output[field];
        },
        getErrors: function (field) {
            for (var key in this.errors) {
                if (key == field) {
                    return this.errors[key];
                }
            }

            return [];
        },
    },
    computed: {
        token: function () {
            return document.head.querySelector('meta[name="csrf-token"]').content;
        },
        jsonOutput: function () {
            return JSON.stringify(this.output);
        },
        layoutId: function () {
            var vm = this;
            var id = 0;
            vm.bread.layouts.forEach(function (layout, key) {
                if (layout.name == vm.layout.name) {
                    id = key;
                }
            });

            return id;
        },
    },
    mounted: function () {
        var vm = this;

        vm.layout.formfields.forEach(function (formfield) {
            if (vm.isFieldTranslatable(formfield.field)) {
                Vue.set(vm.output, formfield.field, vm.get_input_as_translatable_object(vm.output[formfield.field]));
            } else {
                Vue.set(vm.output, formfield.field, vm.output[formfield.field]);
            }
        });

        if (this.translatable.length > 0) {
            this.$language.localePicker = true;
        }
    }
};
</script>