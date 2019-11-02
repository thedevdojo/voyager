<template>
    <div>
        <h2 class="text-3xl mb-5" v-if="action == 'add'">{{ __('voyager::generic.add_type', {type: translate(bread.name_singular, true)}) }}</h2>
        <h2 class="text-3xl mb-5" v-else>{{ __('voyager::bread.edit_name', {name: translate(bread.name_singular, true)}) }}</h2>
        <div class="flex">
            <a class="button green small" :href="route('voyager.'+translate(bread.slug, true)+'.add')" v-if="action == 'edit'">
                {{ __('voyager::generic.add_type', {type: translate(bread.name_singular, true)}) }}
            </a>
            <a class="button blue small" :href="route('voyager.'+translate(bread.slug, true)+'.browse')">
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
                        v-bind:value="data(formfield.column, null)"
                        v-on:input="data(formfield.column, $event)"
                        :primary="data('primary', null)"
                        :bread="bread"
                        :options="formfield.options"
                        :column="formfield.column"
                        :action="action" />
                    <div class="alert red" role="alert" v-if="getErrors(formfield.column).length > 0">
                        <p v-for="(error, key) in getErrors(formfield.column)" :key="'error-'+key">
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
                <button type="submit" name="_redirect" v-if="layout.back_button" value="back" class="button blue">{{ __('voyager::bread.store_name_back', {name: translate(bread.name_singular, true)}) }}</button>
                <button type="submit" name="_redirect" v-if="layout.create_button" value="new" class="button blue">{{ __('voyager::bread.store_name_new', {name: translate(bread.name_singular, true)}) }}</button>
            </div>
            <div v-else class="button-group">
                <button type="submit" name="_redirect" value="" class="button blue">{{ __('voyager::bread.update_name', {name: translate(bread.name_singular, true)}) }}</button>
                <button type="submit" name="_redirect" v-if="layout.back_button" value="back" class="button blue">{{ __('voyager::bread.update_name_back', {name: translate(bread.name_singular, true)}) }}</button>
                <button type="submit" name="_redirect" v-if="layout.create_button" value="new" class="button blue">{{ __('voyager::bread.update_name_new', {name: translate(bread.name_singular, true)}) }}</button>
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
        isColumnTranslatable: function (column) {
            return this.translatable.includes(column);
        },
        data: function (column, value = null) {
            if (value) {
                if (this.isColumnTranslatable(column)) {
                    Vue.set(this.output[column], this.$language.locale, value);
                } else {
                    Vue.set(this.output, column, value);
                }
                this.$globals.$emit('formfield-input', column, value, this.isColumnTranslatable(column));
            }

            if (this.isColumnTranslatable(column)) {
                var translated = this.translate(this.output[column]);

                return translated;
            }

            return this.output[column];
        },
        getErrors: function (column) {
            if (this.isColumnTranslatable(column)) {
                column = column+'.'+this.$language.initial_locale;
            }
            for (var key in this.errors) {
                // TODO: Using startsWith is not necessarily bullet-proof
                if (key.startsWith(column)) {
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
            if (vm.isColumnTranslatable(formfield.column)) {
                Vue.set(vm.output, formfield.column, vm.get_input_as_translatable_object(vm.output[formfield.column]));
            } else {
                Vue.set(vm.output, formfield.column, vm.output[formfield.column]);
            }
        });

        if (this.translatable.length > 0) {
            this.$language.localePicker = true;
        }
    }
};
</script>