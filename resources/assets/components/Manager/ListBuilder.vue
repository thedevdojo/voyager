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
                    <th>{{ __('voyager::generic.actions') }}</th>
                </tr>
            </thead>
            <draggable v-model="layout.formfields" handle=".drag-handle" tag="tbody">
                <tr v-for="(formfield, i) in layout.formfields" v-bind:key="i">
                    <th class="drag-handle"><></th>
                    <th>
                        <select v-model="formfield.options.field" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option v-for="field in fields" v-bind:key="field">{{ field }}</option>
                        </select>
                    </th>
                    <th>{{ $eventHub.getFormfieldByType(formfield.type).name }}</th>
                    <th>
                        <language-input
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            type="text" :placeholder="__('voyager::generic.title')"
                            v-bind:value="formfield.options.title"
                            v-on:input="formfield.options.title = $event" />
                    </th>
                    <th>
                        <input type="checkbox" v-model="formfield.options.searchable">
                    </th>
                    <th>
                        <input type="checkbox" v-model="formfield.options.sortable">
                    </th>
                    <th>
                        <button @click="deleteFormfield(i)" class="bg-blue-400 rounded-sm text-white">{{ __('voyager::generic.delete') }}</button>
                        
                        <popper trigger="click" :options="{ placement: 'left' }">
                            <div class="popper">
                                ...
                            </div>
                            <div slot="reference">
                                <button class="bg-blue-400 rounded-sm text-white">{{ __('voyager::generic.options') }}</button>
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
    props: ['layout', 'fields'],
    data: function () {
        return {
            
        };
    },
    methods: {
        deleteFormfield: function (id) {
            this.$parent.deleteFormfield(id);
        },
    },
    computed: {
        
    }
};
</script>