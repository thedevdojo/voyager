<template>
    <div :class="'p-2 overflow-hidden h-auto w-'+(formfield.options.width || '1/2')">
        <div class="voyager-card">
            <div class="drag-handle cursor-move">
                <div class="w-full text-right">
                    <i class="cursor-pointer" @click="deleteFormfield()"><icon icon="delete" /></i>
                    <i class="cursor-pointer" @click="openOptions()"><icon icon="cog" /></i>
                    <i class="cursor-pointer" @mousedown="startFormfieldResize()" @mouseup="endFormfieldResize()"><icon icon="resize" /></i>
                </div>
                <component
                    :is="'formfield-'+formfield.type"
                    :options="formfield.options"
                    :columns="columns"
                    :computed="computed"
                    :relationships="relationships"
                    type="view"
                    action="mockup" />
            </div>
            <slidein :opened="optionsOpen" v-on:closed="closeOptions">
                <div class="flex mb-4">
                    <div class="w-2/3">
                        <h4 class="text-gray-100 text-lg">{{ __('voyager::generic.options') }}</h4>
                    </div>
                    <div class="w-1/3 text-right text-gray-100">
                        <locale-picker class="mr-2" />
                        <button @click="closeOptions" class="button green">X</button>
                    </div>
                </div>
                <div class="flex mb-4" v-if="hasColumn">
                    <div class="w-full m-1">
                        <label class="voyager-label text-gray-100">{{ __('voyager::generic.column') }}</label>
                        <select v-model="formfield.column" class="voyager-input">
                            <optgroup :label="__('voyager::generic.columns')">
                                <option v-for="column in columns" v-bind:key="column">{{ column }}</option>
                            </optgroup>
                            <optgroup :label="__('voyager::manager.computed')">
                                <option v-for="prop in computed" v-bind:key="prop">{{ prop }}</option>
                            </optgroup>
                        </select>
                    </div>
                </div>
                <div class="flex mb-4" v-if="hasKey">
                    <div class="w-full m-1">
                        <label class="voyager-label text-gray-100">{{ __('voyager::generic.key') }}</label>
                        <input type="text" v-model="formfield.column" class="voyager-input">
                    </div>
                </div>
                <div class="flex mb-4" v-if="hasRelationship">
                    <div class="w-full m-1">
                        <label class="voyager-label text-gray-100">{{ __('voyager::manager.relationship') }}</label>
                        <select v-model="formfield.column" class="voyager-input">
                            <option v-for="(relationship, key) in relationships" v-bind:key="key">{{ key }}</option>
                        </select>
                    </div>
                </div>
                <div class="flex mb-4" v-if="getFormfieldByType(formfield.type).translatable">
                    <div class="w-full m-1">
                        <label class="voyager-label text-gray-100">{{ __('voyager::generic.translatable') }}</label>
                        <input type="checkbox" v-model="formfield.options.translatable">
                    </div>
                </div>
                <div class="flex mb-4" v-if="hasTitle">
                    <div class="w-full m-1">
                        <label class="voyager-label text-gray-100">{{ __('voyager::generic.title') }}</label>
                        <language-input
                            class="voyager-input"
                            type="text" :placeholder="__('voyager::generic.title')"
                            v-bind:value="formfield.options.title"
                            v-on:input="formfield.options.title = $event" />
                    </div>
                </div>
                <div class="flex mb-4" v-if="hasDescription">
                    <div class="w-full m-1">
                        <label class="voyager-label text-gray-100">{{ __('voyager::generic.description') }}</label>
                        <language-input
                            class="voyager-input"
                            type="text" :placeholder="__('voyager::generic.description')"
                            v-bind:value="formfield.options.description"
                            v-on:input="formfield.options.description = $event" />
                    </div>
                </div>
                <component :is="'formfield-'+formfield.type" v-bind:options="formfield.options" :columns="columns" :column="formfield.column" :relationships="relationships" action="options" type="view" />
                <div class="flex mb-4" v-if="hasValidation">
                    <div class="w-full m-1">
                        <bread-validation-input v-bind:rules="formfield.rules" />
                    </div>
                </div>
            </slidein>
        </div>
    </div>
</template>

<script>
export default {
    props: ['formfield', 'columns', 'computed', 'relationships', 'action', 'type', 'repeater'],
    data: function () {
        return {
            hasColumn: true,
            hasKey: false, // Mainly used for repeaters. Instead of having pre-defined columns, let the user enter a text
            hasRelationship: false,
            hasTitle: true,
            hasDescription: true,
            hasValidation: true,
        };
    },
    methods: {
        openOptions: function () {
            this.$parent.$parent.currentOptionsId = this.$vnode.key;
        },
        closeOptions: function () {
            this.$parent.$parent.currentOptionsId = null;
        },
        deleteFormfield: function () {
            this.$parent.$parent.$parent.deleteFormfield(this.$vnode.key);
        },
        startFormfieldResize: function () {
            this.$parent.$parent.startFormfieldResize(this.$vnode.key);
        },
        endFormfieldResize: function () {
            this.$parent.$parent.endFormfieldResize();
        }
    },
    computed: {
        optionsOpen: function () {
            return this.$parent.$parent.currentOptionsId == this.$vnode.key;
        }
    },
    mounted: function () {
        // If coming from repeater, hide the column section automatically
        if (this.repeater) {
            this.hasColumn = false;
            this.hasValidation = false;
            this.hasKey = true;
        }
    }
};
</script>