<template>
    <div class="voyager-table striped mt-0">
        <table>
            <thead>
                <tr>
                    <th class="hidden md:table-cell"></th>
                    <th class="hidden md:table-cell">{{ __('voyager::generic.type') }}</th>
                    <th>{{ __('voyager::generic.column') }}</th>
                    <th>{{ __('voyager::generic.title') }}</th>
                    <th class="hidden md:table-cell">{{ __('voyager::manager.searchable') }}</th>
                    <th class="hidden md:table-cell">{{ __('voyager::manager.orderable') }}</th>
                    <th class="hidden md:table-cell">{{ __('voyager::manager.order_default') }}</th>
                    <th class="hidden md:table-cell">{{ __('voyager::generic.translatable') }}</th>
                    <th style="text-align:right !important">{{ __('voyager::generic.actions') }}</th>
                </tr>
            </thead>
            <draggable v-model="reactiveFormfields" tag="tbody" handle=".draghandle">
                <tr v-for="(formfield, key) in reactiveFormfields" :key="'formfield-'+key">
                    <td class="hidden md:table-cell">
                        <icon icon="direction" class="draghandle cursor-move" />
                    </td>
                    <td class="hidden md:table-cell">{{ getFormfieldByType(formfield.type).name }}</td>
                    <td>
                        <select class="voyager-input small w-full" v-model="formfield.column">
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
                    </td>
                    <td>
                        <language-input
                            class="voyager-input w-full"
                            type="text" placeholder="Title"
                            v-bind:value="formfield.title"
                            v-on:input="formfield.title = $event" />
                    </td>
                    <td class="hidden md:table-cell">
                        <input
                            class="voyager-input"
                            type="checkbox"
                            v-model="formfield.searchable"
                            :disabled="formfield.column.type !== 'column'" />
                    </td>
                    <td class="hidden md:table-cell">
                        <input
                            class="voyager-input"
                            type="checkbox"
                            v-model="formfield.orderable"
                            :disabled="formfield.column.type !== 'column'" />
                    </td>
                    <td class="hidden md:table-cell">
                        <input
                            class="voyager-input"
                            type="radio"
                            :disabled="formfield.column.type !== 'column'"
                            v-model="reactiveOptions.default_order_column"
                            v-bind:value="formfield.column" />
                    </td>
                    <td class="hidden md:table-cell">
                        <input
                            type="checkbox"
                            class="voyager-input"
                            v-model="formfield.translatable"
                            :disabled="!formfield.canbetranslated">
                    </td>
                    <td class="text-right">
                        <button class="button blue" @click="$emit('open-options', key)">
                            <icon icon="cog" />
                            <span>{{ __('voyager::generic.options') }}</span>
                        </button>
                        <button class="button red" @click="$emit('delete', key)">
                            <icon icon="trash" />
                            <span>{{ __('voyager::generic.delete') }}</span>
                        </button>
                        <slide-in :opened="optionsId == key" width="w-full md:w-1/3" v-on:closed="$emit('open-options', null)" class="text-left">
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
                            <component
                                :is="'formfield-'+formfield.type+'-builder'"
                                v-bind:options="formfield.options"
                                :column="formfield.column"
                                show="list-options" />
                        </slide-in>
                    </td>
                </tr>
            </draggable>
        </table>
    </div>
</template>

<script>
export default {
    props: ['computed', 'columns', 'relationships', 'formfields', 'availableFormfields', 'optionsId', 'options'],
    data: function () {
        return {
            reactiveFormfields: this.formfields,
            reactiveOptions: this.options,
        };
    },
    methods: {
        getFormfieldByType: function (type) {
            return this.availableFormfields.filter(function (formfield) {
                return formfield.type == type;
            })[0];
        },
    },
    watch: {
        reactiveFormfields: function (formfields) {
            this.$emit('formfields', formfields);
        },
        reactiveOptions: function (options) {
            this.$emit('options', options);
        }
    },
};
</script>