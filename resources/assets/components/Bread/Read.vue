<template>
    <div>
        <h2 class="text-3xl mb-5">{{ __('voyager::bread.read_name', {name: translate(bread.name_singular, true)}) }}</h2>
        <div class="flex">
            <a class="button green small" :href="route('voyager.'+translate(bread.slug, true)+'.create')">
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
            <div v-for="(formfield, i) in layout.formfields" v-bind:key="'formfield-'+i" :class="'p-2 w-'+formfield.options.width">
                <div class="p-4 bg-white rounded shadow-md ">
                    <label class="voyager-label">{{ translate(formfield.options.title, true) }}</label>
                    <component
                        :is="'formfield-'+formfield.type"
                        :value="getData(formfield.column)"
                        :options="formfield.options"
                        action="read" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['bread', 'accessors', 'layout', 'input', 'translatable', 'prev-url'],
    methods: {
        getData: function (column) {
            if (this.isColumnTranslatable(column)) {
                return this.translate(this.input[column]);
            }

            return this.input[column];
        },
        isColumnTranslatable: function (column) {
            return this.translatable.includes(column);
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
        if (this.translatable.length > 0) {
            Vue.prototype.$language.localePicker = true;
        }
    }
};
</script>