<template>
    <div>
        <h2 class="text-3xl mb-5">{{ __('voyager::bread.read_name', {name: translate(bread.name_singular, true)}) }}</h2>
        <div class="flex">
            <a class="button green small" :href="route('voyager.'+translate(bread.slug, true)+'.add')">
                {{ __('voyager::generic.add_type', {type: translate(bread.name_singular, true)}) }}
            </a>
            <a class="button blue small" :href="route('voyager.'+translate(bread.slug, true)+'.browse')">
                {{ __('voyager::bread.browse_type', {type: translate(bread.name_plural, true)}) }}
            </a>
            <a class="button yellow small" :href="route('voyager.'+translate(bread.slug, true)+'.edit', input.primary)">
                {{ __('voyager::generic.edit_type', {type: translate(bread.name_singular, true)}) }}
            </a>
            <a
                class="button yellow small"
                :href="route('voyager.bread.edit', bread.table)"
                v-if="debug">
                {{ __('voyager::generic.edit_type', {type: __('voyager::bread.bread')}) }}
            </a>
            <a
                class="button yellow small"
                :href="route('voyager.bread.edit', bread.table)+'?layout='+layoutId"
                v-if="debug">
                {{ __('voyager::generic.edit_type', {type: __('voyager::manager.layout')}) }}
            </a>
        </div>
        <br>
        <div class="flex flex-wrap">
            <div v-for="(formfield, i) in layout.formfields" v-bind:key="'formfield-'+i" :class="'p-2 w-'+formfield.options.width">
                <div class="voyager-card">
                    <div class="body">
                        <h3 class="title">{{ translate(formfield.options.title, true) }}</h3>
                        <component
                            :is="'formfield-'+formfield.type"
                            :value="getData(formfield)"
                            :primary="getPrimary()"
                            :bread="bread"
                            :options="formfield.options"
                            :column="formfield.column"
                            action="read" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['bread', 'accessors', 'layout', 'input', 'prev-url'],
    methods: {
        getData: function (formfield) {
            if (formfield.options.translatable || false) {
                return this.translate(this.input[formfield.column]);
            }

            return this.input[formfield.column];
        },
        getPrimary: function () {
            return this.input['primary'];
        }
    },
    computed: {
        layoutId: function () {
            var vm = this;
            var id = 0;
            vm.bread.layouts.forEach(function (layout, key) {
                if (layout.name == vm.layout.name) {
                    id = key;
                }
            });

            return id;
        }
    },
    mounted: function () {
        // TODO: Hide locale picker if there are no translatable formfields
        Vue.prototype.$language.localePicker = true;
    }
};
</script>