<template>
    <div>
        <table class="w-full">
            <thead>
                <tr>
                    <th></th>
                    <th>{{ __('voyager::generic.field') }}</th>
                    <th>{{ __('voyager::generic.type') }}</th>
                    <th>{{ __('voyager::generic.title') }}</th>
                    <th>{{ __('voyager::manager.searchable') }}</th>
                    <th>{{ __('voyager::manager.sortable') }}</th>
                    <th>{{ __('voyager::manager.sorted_by_default') }}</th>
                    <th>{{ __('voyager::manager.link') }}</th>
                    <th>{{ __('voyager::generic.actions') }}</th>
                </tr>
            </thead>
            <draggable v-model="layout.formfields" handle=".drag-handle" tag="tbody" @end="refresh()">
                <tr v-for="(formfield, i) in layout.formfields" v-bind:key="i">
                    <th class="drag-handle">&lt;&gt;</th>
                    <th>
                        <select v-model="formfield.options.field" class="voyager-input">
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
                    <th>{{ $eventHub.getFormfieldByType(formfield.type).name }}</th>
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
                            :disabled="computed.includes(formfield.options.field)">
                    </th>
                    <th>
                        <input
                            type="checkbox"
                            v-model="formfield.options.sortable"
                            :disabled="computed.includes(formfield.options.field)">
                    </th>
                    <th>
                        <input
                            type="radio"
                            name="default_sorted"
                            v-model="layout.default_sort_field"
                            :value="formfield.options.field"
                            :disabled="computed.includes(formfield.options.field)">
                    </th>
                    <th>
                        <input
                            type="checkbox"
                            v-model="formfield.options.link">
                    </th>
                    <th class="inline flex">
                        <button @click="deleteFormfield(i)" class="voyager-button red small">{{ __('voyager::generic.delete') }}</button>
                        
                        <popper trigger="click" :options="{ placement: 'left' }">
                            <div class="popper">
                                ...
                            </div>
                            <div slot="reference">
                                <button class="voyager-button blue small">{{ __('voyager::generic.options') }}</button>
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
    data: function () {
        return {
            
        };
    },
    methods: {
        deleteFormfield: function (id) {
            this.$parent.deleteFormfield(id);
        },
        refresh: function () {
            this.$forceUpdate();
        },
    },
    computed: {
        
    }
};
</script>