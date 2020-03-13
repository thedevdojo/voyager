<template>
    <div>
        <div class="voyager-table striped">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('voyager::generic.type') }}</th>
                        <th>{{ __('voyager::generic.column') }}</th>
                        <th>{{ __('voyager::generic.title') }}</th>
                        <th>{{ __('voyager::manager.searchable') }}</th>
                        <th>{{ __('voyager::manager.sortable') }}</th>
                        <th>{{ __('voyager::manager.sorted_by_default') }}</th>
                        <th>{{ __('voyager::manager.link') }}</th>
                        <th class="text-right">{{ __('voyager::generic.actions') }}</th>
                    </tr>
                </thead>
                <draggable v-model="layout.formfields" handle=".drag-handle" tag="tbody" :animation="200" ghost-class="ghost">
                    <tr v-for="(formfield, i) in layout.formfields" :key="i" class="drag-handle cursor-move">
                        <td>{{ getFormfieldByType(formfield.type).name }}</td>
                        <td>
                            <select v-model="formfield.column" class="voyager-input">
                                <optgroup :label="__('voyager::generic.columns')">
                                    <option v-for="column in columns" v-bind:key="column">{{ column }}</option>
                                </optgroup>
                                <optgroup :label="__('voyager::manager.computed')" v-if="computed.length > 0">
                                    <option v-for="prop in computed" v-bind:key="prop">{{ prop }}</option>
                                </optgroup>
                                <optgroup v-for="(relationship, name) in relationships" v-bind:key="name" :label="name">
                                    <option v-for="column in relationship.columns" v-bind:key="name+'.'+column">{{ name }}.{{ column }}</option>
                                    <option v-for="pivot in relationship.pivot" v-bind:key="name+'.pivot.'+pivot">{{ name }}.pivot.{{ pivot }}</option>
                                </optgroup>
                            </select>
                        </td>
                        <td>
                            <language-input
                                class="voyager-input"
                                type="text" :placeholder="__('voyager::generic.title')"
                                v-bind:value="formfield.options.title"
                                v-on:input="formfield.options.title = $event" />
                        </td>
                        <td>
                            <input
                                type="checkbox"
                                class="voyager-input"
                                v-model="formfield.options.searchable"
                                :disabled="computed.includes(formfield.column)">
                        </td>
                        <td>
                            <input
                                type="checkbox"
                                class="voyager-input"
                                v-model="formfield.options.sortable"
                                :disabled="computed.includes(formfield.column) || (formfield.column || '').includes('.')">
                            <popper
                                trigger="clickToOpen"
                                :options="{ placement: 'bottom' }"
                                v-if="computed.includes(formfield.column) || (formfield.column || '').includes('.')">
                                <div class="popper" v-html="__('voyager::manager.relationship_sort_notice')"></div>
                                <span slot="reference" class="popper-ref">[?]</span>
                            </popper>
                        </td>
                        <td>
                            <input
                                type="radio"
                                class="voyager-input"
                                name="default_sorted"
                                v-model="layout.default_sort_column"
                                :value="formfield.column"
                                :disabled="computed.includes(formfield.column) || (formfield.column || '').includes('.')">
                            <popper
                                trigger="clickToOpen"
                                :options="{ placement: 'bottom' }"
                                v-if="computed.includes(formfield.column) || (formfield.column || '').includes('.')">
                                <div class="popper" v-html="__('voyager::manager.relationship_sort_notice')"></div>
                                <span slot="reference" class="popper-ref">[?]</span>
                            </popper>
                        </td>
                        <td>
                            <select class="voyager-input" v-model="formfield.options.link" v-if="!(formfield.column || '').includes('.')">
                                <option :value="false">{{ __('voyager::generic.no') }}</option>
                                <option value="read">{{ __('voyager::generic.read') }}</option>
                                <option value="read_new">{{ __('voyager::generic.read') }} ({{ __('voyager::generic.new_window') }})</option>
                                <option value="edit">{{ __('voyager::generic.edit') }}</option>
                                <option value="edit_new">{{ __('voyager::generic.edit') }} ({{ __('voyager::generic.new_window') }})</option>
                            </select>
                            <select class="voyager-input" v-model="formfield.options.link" v-else>
                                <!-- TODO: Check if relationship has BREAD and allow to link to it -->
                            </select>
                        </td>
                        <td class="text-right">
                            <div class="inline-flex">
                                <button @click="deleteFormfield(i)" class="button red small">{{ __('voyager::generic.delete') }}</button>
                                <slidein :opened="currentOptionsId == i" v-on:closed="currentOptionsId = null" class="text-left">
                                    <div class="flex mb-4">
                                        <div class="w-2/3">
                                            <h4 class="text-gray-100 text-lg">{{ __('voyager::generic.options') }}</h4>
                                        </div>
                                        <div class="w-1/3 text-right text-gray-100">
                                            <locale-picker class="mr-2" />
                                            <button @click="currentOptionsId = null" class="button green">X</button>
                                        </div>
                                    </div>
                                    <div class="flex mb-4" v-if="getFormfieldByType(formfield.type).translatable">
                                        <div class="w-full m-1">
                                            <label class="voyager-label text-gray-100">{{ __('voyager::generic.translatable') }}</label>
                                            <input type="checkbox" v-model="formfield.options.translatable">
                                        </div>
                                    </div>
                                    <component :is="'formfield-'+formfield.type" v-bind:options="formfield.options" :columns="columns" action="options" type="list" />
                                </slidein>
                                <button class="button blue small" @click="currentOptionsId = i">{{ __('voyager::generic.options') }}</button>
                            </div>
                        </td>
                    </tr>
                </draggable>
            </table>
        </div>
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
<style scoped>
.ghost {
    @apply opacity-50;
}
</style>