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
                    <th class="drag-handle cursor-pointer">
                        <icon icon="up-down" />
                    </th>
                    <th>{{ getFormfieldByType(formfield.type).name }}</th>
                    <th class="p-1">
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
                    <th class="p-1">
                        <language-input
                            class="voyager-input"
                            type="text" :placeholder="__('voyager::generic.title')"
                            v-bind:value="formfield.options.title"
                            v-on:input="formfield.options.title = $event" />
                    </th>
                    <th class="p-1">
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
                            :disabled="computed.includes(formfield.column) || (formfield.column || '').includes('.')">
                    </th>
                    <th>
                        <input
                            type="radio"
                            name="default_sorted"
                            v-model="layout.default_sort_column"
                            :value="formfield.column"
                            :disabled="computed.includes(formfield.column) || (formfield.column || '').includes('.')">
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
                    <th class="flex justify-center">
                        <button @click="deleteFormfield(i)" class="button red small">{{ __('voyager::generic.delete') }}</button>
                        <slidein :opened="currentOptionsId == i" v-on:closed="currentOptionsId = null" class="text-left">
                            <component :is="'formfield-'+formfield.type" v-bind:options="formfield.options" :columns="columns" action="options" type="list" />
                            <div class="flex mb-4">
                                <div class="w-full m-1">
                                    <label class="voyager-label text-base text-gray-100">{{ __('voyager::manager.validation_rules') }}</label>
                                    <small class="text-xs text-gray-300">{{ __('voyager::manager.validation_rules_hint') }}</small>
                                    <bread-validation-input v-bind:rules="formfield.rules" />
                                </div>
                            </div>
                        </slidein>
                        <button class="button blue small" @click="currentOptionsId = i">{{ __('voyager::generic.options') }}</button>
                    </th>
                </tr>
            </draggable>
        </table>
        <slidein :opened="showSettings" v-on:closed="$emit('layout-settings-closed')">
            <div class="flex mb-4">
                <div class="w-2/3">
                    <h4 class="text-gray-100 text-lg">{{ __('voyager::generic.options') }}</h4>
                </div>
                <div class="w-1/3 text-right text-gray-100">
                    <button @click="$emit('layout-settings-closed')" class="button green">X</button>
                </div>
            </div>
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::manager.soft_deletes') }}</label>
                    <select class="voyager-input" v-model="layout.soft_deletes">
                        <option v-bind:value="'hide'">{{ __('voyager::generic.hide') }}</option>
                        <option v-bind:value="'show'">{{ __('voyager::generic.show') }}</option>
                        <option v-bind:value="'select'">{{ __('voyager::generic.select') }}</option>
                        <option v-bind:value="'only'">{{ __('voyager::generic.only') }}</option>
                    </select>
                </div>
            </div>
            <div class="flex mb-4">
                <div class="w-1/2 m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::manager.allow_restore') }}</label>
                    <input type="checkbox" v-model="layout.restore">
                </div>
                <div class="w-1/2 m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::manager.allow_force_delete') }}</label>
                    <input type="checkbox" v-model="layout.force_delete">
                </div>
            </div>
            <label class="voyager-label text-gray-100">{{ __('voyager::manager.soft_deletes_help') }}</label>
        </slidein>
    </div>
</template>

<script>
export default {
    props: ['layout', 'columns', 'computed', 'relationships', 'show-settings'],
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