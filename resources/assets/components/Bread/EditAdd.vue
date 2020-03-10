<template>
    <div>
        <h2 class="text-3xl mb-5" v-if="action == 'add'">{{ __('voyager::generic.add_type', {type: translate(bread.name_singular, true)}) }}</h2>
        <h2 class="text-3xl mb-5" v-else>{{ __('voyager::generic.edit_type', {type: translate(bread.name_singular, true)}) }}</h2>
        <div class="flex">
            <a class="button green small" :href="route('voyager.'+translate(bread.slug, true)+'.add')" v-if="action == 'edit' && !(fromRelationship || false)">
                {{ __('voyager::generic.add_type', {type: translate(bread.name_singular, true)}) }}
            </a>
            <a class="button blue small" :href="route('voyager.'+translate(bread.slug, true)+'.browse')" v-if="!(fromRelationship || false)">
                {{ __('voyager::bread.browse_type', {type: translate(bread.name_plural, true)}) }}
            </a>
            <a
                class="button yellow small inline"
                :href="route('voyager.bread.edit', bread.table)"
                v-if="debug">
                {{ __('voyager::generic.edit_type', {type: __('voyager::bread.bread')}) }}
            </a>
            <a
                class="button yellow small inline"
                :href="route('voyager.bread.edit', bread.table)+'?layout='+layoutId"
                v-if="debug">
                {{ __('voyager::generic.edit_type', {type: __('voyager::manager.layout')}) }}
            </a>
        </div>
        <br>
        <div class="flex flex-wrap">
            <div v-for="(formfield, i) in layout.formfields" v-bind:key="'formfield-'+i" :class="'w-full lg:w-'+formfield.options.width+' '+(fromRelationship || false ? 'mode-dark' : '')">
                <div class="voyager-card">
                    <div class="body">
                        <component
                            :is="'formfield-'+formfield.type"
                            v-bind:value="data(formfield, null)"
                            v-on:input="data(formfield, $event)"
                            :primary="primaryKey"
                            :bread="bread"
                            :options="formfield.options"
                            :column="formfield.column"
                            :relationships="relationships"
                            :action="action" />
                        <div class="alert red" role="alert" v-if="getErrors(formfield).length > 0">
                            <p v-for="(error, key) in getErrors(formfield)" :key="'error-'+key">
                                {{ error }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <form method="post" :action="url">
            <input type="hidden" name="_token" :value="$globals.csrf_token">
            <input type="hidden" name="_method" :value="action == 'edit' ? 'PUT' : 'POST'">
            <input type="hidden" name="data" :value="jsonOutput">
            <button @click="submitForm" type="submit" class="button blue">
                {{ __('voyager::bread.'+(action == 'add' ? 'store' : 'update')+'_type', {type: translate(bread.name_singular, true)}) }}
            </button>
        </form>
    </div>
</template>

<script>
export default {
    props: ['bread', 'action', 'errors', 'accessors', 'relationships', 'layout', 'input', 'url', 'prev-url', 'from-relationship'],
    data: function () {
        return {
            validation_errors: this.errors,
            output: this.input,
        };
    },
    methods: {
        data: function (formfield, value = null) {
            if (value) {
                if (formfield.options.translatable || false) {
                    Vue.set(this.output[formfield.column], this.$language.locale, value);
                } else {
                    Vue.set(this.output, formfield.column, value);
                }
                this.$globals.$emit('formfield-input', formfield.column, value, formfield.options.translatable);
            }

            if (formfield.options.translatable) {
                var translated = this.translate(this.output[formfield.column]);

                return translated;
            }

            return this.output[formfield.column];
        },
        getErrors: function (formfield) {
            var column = formfield.column;
            if (formfield.options.translatable || false) {
                column = formfield.column+'.'+this.$language.initial_locale;
            }
            for (var key in this.validation_errors) {
                // TODO: Using startsWith is not necessarily bullet-proof
                if (key.startsWith(column)) {
                    return this.validation_errors[key];
                }
            }

            return [];
        },
        submitForm: function (e) {
            if (!(this.bread.ajax_validation || true)) {
                return;
            }

            e.preventDefault();

            var vm = this;
            axios.post(vm.url, {
                data: vm.jsonOutput,
                _method: (vm.action == 'edit' ? 'PUT' : 'POST')
            })
            .then(function (response) {
                var primary = response.data;
                vm.validation_errors = [];
                if (vm.action == 'add') {
                    if (!vm.fromRelationship) {
                        // Redirect to edit-page
                        window.location.href = vm.route('voyager.'+vm.translate(vm.bread.slug, true)+'.edit', primary);
                    }
                    vm.$snotify.success(vm.__('voyager::bread.success_stored_type', {type: vm.translate(vm.bread.name_singular, true)}));
                } else {
                    vm.$snotify.success(vm.__('voyager::bread.success_updated_type', {type: vm.translate(vm.bread.name_singular, true)}));
                }

                if ((vm.fromRelationship || false)) {
                    // First parent is the modal, second parent is the relationship-formfield
                    vm.$parent.$parent.finishAddingEntry(primary);
                    vm.resetForm();
                }
            })
            .catch(function (errors) {
                vm.$snotify.error(vm.__('voyager::bread.validation_error_msg'));
                if (errors.response) {
                    vm.validation_errors = errors.response.data;
                } else {
                    console.error(errors);
                }
            });
        },
        resetForm: function () {
            var vm = this;
            vm.layout.formfields.forEach(function (formfield) {
                if (formfield.options.translatable || false) {
                    Vue.set(vm.output, formfield.column, vm.get_input_as_translatable_object(''));
                } else {
                    Vue.set(vm.output, formfield.column, '');
                }
            });
        }
    },
    computed: {
        primaryKey: function () {
            return this.output['primary'];
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
            if (formfield.options.translatable || false) {
                Vue.set(vm.output, formfield.column, vm.get_input_as_translatable_object(vm.output[formfield.column]));
            } else {
                Vue.set(vm.output, formfield.column, vm.output[formfield.column]);
            }
        });

        // TODO: Hide locale picker if there are no translatable formfields
        this.$language.localePicker = true;
    },
};
</script>