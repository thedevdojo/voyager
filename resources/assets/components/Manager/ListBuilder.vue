<template>
    <div>
        <table class="w-full">
            <thead>
                <tr>
                    <th></th>
                    <th>{{ __('voyager::generic.type') }}</th>
                    <th>{{ __('voyager::generic.field') }}</th>
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
                        <select v-model="formfield.field" class="voyager-input">
                            <optgroup :label="__('voyager::generic.fields')">
                                <option v-for="field in fields" v-bind:key="field">{{ field }}</option>
                            </optgroup>
                            <optgroup :label="__('voyager::manager.computed')">
                                <option v-for="prop in computed" v-bind:key="prop">{{ prop }}</option>
                            </optgroup>
                            <optgroup v-for="(relationship, name) in relationships" v-bind:key="name" :label="name">
                                <option v-for="field in relationship.fields" v-bind:key="name+'.'+field">{{ name }}.{{ field }}</option>
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
                            :disabled="computed.includes(formfield.field)">
                    </th>
                    <th>
                        <input
                            type="radio"
                            name="global_search"
                            v-model="layout.global_search"
                            :value="formfield.field">
                    </th>
                    <th>
                        <input
                            type="checkbox"
                            v-model="formfield.options.sortable"
                            :disabled="computed.includes(formfield.field)">
                    </th>
                    <th>
                        <input
                            type="radio"
                            name="default_sorted"
                            v-model="layout.default_sort_field"
                            :value="formfield.field"
                            :disabled="computed.includes(formfield.field)">
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
                        
                        <popper trigger="click" :options="{ placement: 'left' }">
                            <div class="popper">
                                ...
                            </div>
                            <div slot="reference">
                                <button class="button blue small">{{ __('voyager::generic.options') }}</button>
                            </div>
                        </popper>
                    </th>
                </tr>
            </draggable>
        </table>
    </div>
</template>

<script>
export default {
    props: ['layout', 'fields', 'computed', 'relationships'],
    methods: {
        deleteFormfield: function (id) {
            this.$parent.deleteFormfield(id);
        },
    },
};
</script>