<template>
    <div :class="'p-2 overflow-hidden h-auto w-'+(formfield.options.width || '1/2')">
        <div class="p-4 bg-white rounded shadow-md ">
            <div class="w-full text-right">
                <a class="" @click="deleteFormfield()">X</a>
                <a class="" @click="openOptions()">O</a>
                <a class="drag-handle">D</a>
                <a class="" @mousedown="startFormfieldResize()" @mouseup="endFormfieldResize()">&lt;&gt;</a>
            </div>
            <component :is="'formfield-'+formfield.type" :options="formfield.options" :columns="columns" action="mockup" />
            <slidein :opened="optionsOpen" v-on:closed="closeOptions">
                <div class="">
                    <locale-picker></locale-picker>
                    <br>
                    <div class="flex mb-4">
                        <div class="w-2/3">
                            <h4 class="text-gray-100 text-lg">{{ __('voyager::generic.options') }}</h4>
                        </div>
                        <div class="w-1/3 text-right text-gray-100">
                            <a @click="closeOptions()" class="cursor-pointer">X</a>
                        </div>
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
                <div class="flex mb-4" v-if="hasRelationship">
                    <div class="w-full m-1">
                        <label class="voyager-label text-gray-100">{{ __('voyager::manager.relationship') }}</label>
                        <select v-model="formfield.column" class="voyager-input">
                            <option v-for="(relationship, key) in relationships" v-bind:key="key">{{ key }}</option>
                        </select>
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
    props: ['formfield', 'columns', 'computed', 'relationships', 'action', 'type'],
    data: function () {
        return {
            hasColumn: true,
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
    }
};
</script>