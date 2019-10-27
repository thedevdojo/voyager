<template>
    <div>
        <div v-if="action == 'edit' || action == 'add' || action == 'read'" class="flex mb-4">
            <div class="w-full m-1">
                <component :is="options.type">
                    {{ translate(options.content) }}
                </component>
            </div>
        </div>
        <div v-else-if="action == 'mockup'" class="flex mb-4">
            <div class="w-full m-1">
                <component :is="options.type">
                    {{ translate(options.content) }}
                </component>
            </div>
        </div>
        <div v-else-if="action == 'options'">
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">Type</label>
                    <select class="voyager-input" v-model="options.type">
                        <option value="hr">Horizontal Rule</option>
                        <option value="h1">Heading 1</option>
                        <option value="h2">Heading 2</option>
                        <option value="h3">Heading 3</option>
                        <option value="h4">Heading 4</option>
                    </select>
                </div>
            </div>
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::generic.content') }}</label>
                    <language-input
                        class="voyager-input"
                        type="text" :placeholder="__('voyager::generic.content')"
                        v-model="options.content" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['value', 'options', 'columns', 'action', 'type'],
    mounted: function () {
        this.$parent.hasColumn = false;
        this.$parent.hasRelationship = false;
        this.$parent.hasTitle = false;
        this.$parent.hasDescription = false;
        this.$parent.hasValidation = false;
    }
};
</script>