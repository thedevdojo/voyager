<template>
    <div :class="'p-2 overflow-hidden h-auto w-'+(formfield.options.width || '1/2')">
        <div class="p-4 bg-white rounded shadow-md ">
            <div class="w-full text-right">
                <a class="" @click="deleteFormfield()">X</a>
                <a class="" @click="openOptions()">O</a>
                <a class="drag-handle">D</a>
                <a class="" @mousedown="startFormfieldResize()" @mouseup="endFormfieldResize()">&lt;&gt;</a>
            </div>
            <component :is="'formfield-'+formfield.type" :options="formfield.options" :fields="fields" action="mockup" />
            <popper trigger="click" :force-show="optionsOpen" :options="{ placement: 'top' }">
                <div class="popper">
                    <div class="flex mb-4">
                        <div class="w-2/3">
                            <h4 class="text-gray-100 text-lg">Options</h4>
                        </div>
                        <div class="w-1/3 text-right text-gray-100">
                            <a @click="closeOptions()" class="cursor-pointer">X</a>
                        </div>
                    </div>
                    <div class="flex mb-4">
                        <div class="w-full m-1" v-if="hasField">
                            <label class="block text-gray-100 text-sm font-bold mb-2">{{ __('voyager::generic.field') }}</label>
                            <select v-model="formfield.options.field" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <optgroup :label="__('voyager::generic.fields')">
                                    <option v-for="field in fields" v-bind:key="field">{{ field }}</option>
                                </optgroup>
                                <optgroup :label="__('voyager::manager.accessors')">
                                    <option v-for="accessor in accessors" v-bind:key="accessor">{{ accessor }}</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    <div class="flex mb-4">
                        <div class="w-full m-1" v-if="hasTitle">
                            <label class="block text-gray-100 text-sm font-bold mb-2">{{ __('voyager::generic.title') }}</label>
                            <language-input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                type="text" :placeholder="__('voyager::generic.title')"
                                v-bind:value="formfield.options.title"
                                v-on:input="formfield.options.title = $event" />
                        </div>
                    </div>
                    <div class="flex mb-4">
                        <div class="w-full m-1" v-if="hasDescription">
                            <label class="block text-gray-100 text-sm font-bold mb-2">{{ __('voyager::generic.description') }}</label>
                            <language-input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                type="text" :placeholder="__('voyager::generic.description')"
                                v-bind:value="formfield.options.description"
                                v-on:input="formfield.options.description = $event" />
                        </div>
                    </div>
                    <component :is="'formfield-'+formfield.type" v-bind:options="formfield.options" :fields="fields" action="options" />
                </div>
                <div slot="reference"></div>
            </popper>
        </div>
    </div>
</template>

<script>
export default {
    props: ['formfield', 'fields', 'accessors', 'relationships', 'action', 'type'],
    data: function () {
        return {
            hasField: true,
            hasTitle: true,
            hasDescription: true,
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