<template>
    <div>
        <table class="w-full">
            <thead>
                <tr>
                    <th></th>
                    <th>{{ __('voyager::generic.type') }}</th>
                    <th>{{ __('voyager::generic.column') }}</th>
                    <th>{{ __('voyager::generic.title') }}</th>
                    <th>{{ __('voyager::manager.searchable') }}</th>
                    <th>{{ __('voyager::manager.show_in_global_search') }}</th>
                    <th>{{ __('voyager::manager.sortable') }}</th>
                    <th>{{ __('voyager::manager.sorted_by_default') }}</th>
                    <th>{{ __('voyager::manager.link') }}</th>
                    <th>{{ __('voyager::generic.actions') }}</th>
                </tr>
            </thead>
            <draggable v-model="layout.formfields" handle=".drag-handle" tag="tbody">
                <tr v-for="(formfield, i) in layout.formfields" :key="i">
                    <th class="drag-handle">&lt;&gt;</th>
                    <th>{{ getFormfieldByType(formfield.type).name }}</th>
                    <th>
                        <select v-model="formfield.column" class="voyager-input">
                            <optgroup :label="__('voyager::generic.columns')">
                                <option v-for="column in columns" v-bind:key="column">{{ column }}</option>
                            </optgroup>
                            <optgroup :label="__('voyager::manager.computed')">
                                <option v-for="prop in computed" v-bind:key="prop">{{ prop }}</option>
                            </optgroup>
                            <optgroup v-for="(relationship, name) in relationships" v-bind:key="name" :label="name">
                                <option v-for="column in relationship.columns" v-bind:key="name+'.'+column">{{ name }}.{{ column }}</option>
                                <option v-for="pivot in relationship.pivot" v-bind:key="name+'.pivot.'+pivot">{{ name }}.pivot.{{ pivot }}</option>
                            </optgroup>
                        </select>
                    </th>
                    <th>
                        <language-input
                            class="voyager-input"
                            type="text" :placeholder="__('voyager::generic.title')"
                            v-bind:value="formfield.options.title"
                            v-on:input="formfield.options.title = $event" />
                    </th>
                    <th>
                        <input
                            type="checkbox"
                            v-model="formfield.options.searchable"
                            :disabled="computed.includes(formfield.column)">
                    </th>
                    <th>
                        <input
                            type="radio"
                            name="global_search"
                            v-model="layout.global_search"
                            :value="formfield.column">
                    </th>
                    <th>
                        <input
                            type="checkbox"
                            v-model="formfield.options.sortable"
                            :disabled="computed.includes(formfield.column) || formfield.column.includes('.')">
                    </th>
                    <th>
                        <input
                            type="radio"
                            name="default_sorted"
                            v-model="layout.default_sort_column"
                            :value="formfield.column"
                            :disabled="computed.includes(formfield.column) || formfield.column.includes('.')">
                    </th>
                    <th>
                        <select class="voyager-input" v-model="formfield.options.link">
                            <option :value="false">{{ __('voyager::generic.no') }}</option>
                            <option value="read">{{ __('voyager::generic.read') }}</option>
                            <option value="read_new">{{ __('voyager::generic.read') }} ({{ __('voyager::generic.new_window') }})</option>
                            <option value="edit">{{ __('voyager::generic.edit') }}</option>
                            <option value="edit_new">{{ __('voyager::generic.edit') }} ({{ __('voyager::generic.new_window') }})</option>
                        </select>
                    </th>
                    <th class="flex">
                        <button @click="deleteFormfield(i)" class="button red small">{{ __('voyager::generic.delete') }}</button>
                        <slidein :opened="currentOptionsId == i" v-on:closed="currentOptionsId = null">
                            <component :is="'formfield-'+formfield.type" v-bind:options="formfield.options" :columns="columns" action="options" type="list" />
                            <div class="flex mb-4">
                                <!-- TODO: Add hint that validation rules are only necessary if this is a relationship-list -->
                                <div class="w-full m-1">
                                    <bread-validation-input v-bind:rules="formfield.rules" />
                                </div>
                            </div>
                        </slidein>
                        <button class="button blue small" @click="currentOptionsId = i">{{ __('voyager::generic.options') }}</button>
                    </th>
                </tr>
            </draggable>
        </table>
    </div>
</template>

<script>
export default {
    props: ['layout', 'columns', 'computed', 'relationships'],
    data: function () {
        return {
            currentOptionsId: null
        };
    },
    methods: {
        deleteFormfield: function (id) {
            this.$parent.deleteFormfield(id);
        },
    },
};
</script>