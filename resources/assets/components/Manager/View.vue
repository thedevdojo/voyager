<template>
    <draggable
        class="flex flex-wrap w-full"
        tag="div"
        handle=".draghandle"
        :list="reactiveFormfields"
        :group="{ name: 'formfields' }">
        <div
            v-for="(formfield, key) in reactiveFormfields"
            :key="'formfield-'+key"
            class="m-0"
            :class="formfield.options.width">
            <card :title="formfield.column.column || 'No column'">
                <div slot="actions">
                    <button class="button small blue icon-only">
                        <icon icon="expand-arrows" class="draghandle cursor-move" />
                    </button>
                    <button class="button small blue icon-only" @mousedown="startResize(key)">
                        <icon icon="arrows-h" class="cursor-move" />
                    </button>
                    <button class="button small blue icon-only" @click="$emit('open-options', key)">
                        <icon icon="cog" />
                    </button>
                    <button class="button small red icon-only" @click="$emit('delete', key)">
                        <icon icon="trash" />
                    </button>
                    <slidein :opened="optionsId == key" v-on:closed="$emit('open-options', null)" width="w-1/3" class="text-left">
                        <div class="flex w-full mb-3">
                            <div class="w-1/2 text-2xl">
                                <h2>{{ __('voyager::generic.options') }}</h2>
                            </div>
                            <div class="w-1/2 flex justify-end">
                                <locale-picker v-if="$language.localePicker" />
                                <button class="button green icon-only" @click="$emit('open-options', null)">
                                    <icon icon="times" />
                                </button>
                            </div>
                        </div>
                        <label class="label mt-4">{{ __('voyager::generic.column') }}</label>
                        <select class="voyager-input w-full" v-model="formfield.column">
                            <optgroup :label="__('voyager::manager.columns')">
                                <option v-for="(column, i) in columns" :key="'column_'+i" :value="{column: column, type: 'column'}">
                                    {{ column }}
                                </option>
                            </optgroup>
                            <optgroup :label="__('voyager::manager.computed')">
                                <option v-for="(prop, i) in computed" :key="'computed_'+i" :value="{column: prop, type: 'computed'}">
                                    {{ prop }}
                                </option>
                            </optgroup>
                            <optgroup v-for="(relationship, i) in relationships" :key="'relationship_'+i" :label="relationship.method">
                                <option v-for="(column, i) in relationship.columns" :key="'column_'+i" :value="{column: relationship.method+'.'+column, type: 'relationship'}">
                                    {{ column }}
                                </option>
                            </optgroup>
                        </select>

                        <component
                            :is="'formfield-'+formfield.type+'-builder'"
                            v-bind:options="formfield.options"
                            :column="formfield.column"
                            show="view-options" />
                        <bread-manager-validation v-model="formfield.validation" />
                    </slidein>
                </div>

                <component
                    :is="'formfield-'+formfield.type+'-builder'"
                    v-bind:options="formfield.options"
                    :column="formfield.column"
                    show="view" />
            </card>
        </div>
        <slot v-if="reactiveFormfields.length == 0" />
    </draggable>
</template>

<script>
export default {
    props: ['computed', 'columns', 'relationships', 'formfields', 'availableFormfields', 'optionsId', 'options'],
    data: function () {
        return {
            reactiveFormfields: this.formfields,
            reactiveOptions: this.options,
            resizingFormfield: null,
            sizes: [
                'w-1/6',
                'w-2/6',
                'w-3/6',
                'w-4/6',
                'w-5/6',
                'w-full',
            ]
        };
    },
    methods: {
        startResize: function (key) {
            this.resizingFormfield = key;
        }
    },
    watch: {
        reactiveFormfields: function (formfields) {
            this.$emit('formfields', formfields);
        },
        reactiveOptions: function (options) {
            this.$emit('options', options);
        }
    },
    mounted: function () {
        var vm = this;

        window.addEventListener('mouseup', function () {
            vm.resizingFormfield = null;
        });

        this.$el.addEventListener('mousemove', function (e) {
            if (vm.resizingFormfield !== null) {
                e.preventDefault();
                var rect = vm.$el.getBoundingClientRect();
                var x = e.clientX - rect.left - 50;
                var threshold = rect.width / (vm.sizes.length - 1);
                var size = Math.min(Math.max(Math.ceil(x / threshold), 0), vm.sizes.length);
                Vue.set(vm.formfields[vm.resizingFormfield].options, 'width', vm.sizes[size]);
            }
        });
    }
};
</script>