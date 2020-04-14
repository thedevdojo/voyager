<template>
    <div class="voyager-table striped mt-0">
        <table>
            <thead>
                <tr>
                    <th class="hidden md:table-cell"></th>
                    <th class="hidden md:table-cell">Type</th>
                    <th>Column</th>
                    <th>Title</th>
                    <th class="hidden md:table-cell">Searchable</th>
                    <th class="hidden md:table-cell">Orderable</th>
                    <th class="hidden md:table-cell">Ordered by default</th>
                    <th style="text-align:right !important">Actions</th>
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
                            <optgroup label="Columns">
                                <option v-for="(column, i) in columns" :key="'column_'+i" :value="{column: column, type: 'column'}">
                                    {{ column }}
                                </option>
                            </optgroup>
                            <optgroup label="Computed">
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
                            v-model="formfield.searchable" />
                    </td>
                    <td class="hidden md:table-cell">
                        <input
                            class="voyager-input"
                            type="checkbox"
                            v-model="formfield.orderable" />
                    </td>
                    <td class="hidden md:table-cell">
                        <input
                            class="voyager-input"
                            type="radio"
                            v-model="reactiveOptions.default_order_column"
                            v-bind:value="formfield.column" />
                    </td>
                    <td class="text-right">
                        <button class="button blue" @click="$emit('open-options', key)">
                            <icon icon="cog" />
                            <span>Options</span>
                        </button>
                        <button class="button red" @click="$emit('delete', key)">
                            <icon icon="trash" />
                            <span>Delete</span>
                        </button>
                        <slidein :opened="optionsId == key" width="w-full md:w-1/3" v-on:closed="$emit('open-options', null)" class="text-left">
                            <div class="flex w-full mb-3">
                                <div class="w-1/2 text-2xl">
                                    <h2>Options</h2>
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
                            <bread-manager-validation v-model="formfield.validation" />
                        </slidein>
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