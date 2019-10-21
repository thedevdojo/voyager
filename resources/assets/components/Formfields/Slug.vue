<template>
    <div>
        <div v-if="action == 'browse' || action == 'read'">
            {{ value }}
        </div>
        <div v-else-if="action == 'edit' || action == 'add'" class="flex mb-4">
            <div class="w-full m-1">
                <label class="voyager-label">{{ translate(options.title, true) }}</label>
                <input
                    type="text"
                    :disabled="options.disabled || false"
                    :placeholder="translate(options.placeholder, true)"
                    v-bind:value="slugify(value)"
                    v-on:input="$emit('input', $event.target.value);"
                    class="voyager-input" />
                <p>{{ translate(options.description, true) }}</p>
            </div>
        </div>
        <div v-else-if="action == 'mockup'" class="flex mb-4">
            <div class="w-full m-1">
                <label class="voyager-label">{{ translate(options.title) }}</label>
                <input
                    type="text"
                    :disabled="action == 'mockup' || options.disabled"
                    :placeholder="translate(options.placeholder)"
                    :value="translate(options.default_value)"
                    class="voyager-input" />
                <p>{{ translate(options.description) }}</p>
            </div>
        </div>
        <div v-else-if="action == 'options'">
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::generic.placeholder') }}</label>
                    <language-input
                        class="voyager-input"
                        type="text" :placeholder="__('voyager::generic.placeholder')"
                        v-bind:value="options.placeholder"
                        v-on:input="options.placeholder = $event" />
                </div>
            </div>
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::generic.default_value') }}</label>
                    <language-input
                        class="voyager-input"
                        type="text" :placeholder="__('voyager::generic.default_value')"
                        v-model="options.default_value" />
                </div>
            </div>
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::generic.disabled') }}</label>
                    <input type="checkbox" v-model="options.disabled">
                </div>
            </div>
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::generic.from') }}</label>
                    <select v-model="options.from" class="voyager-input">
                        <option v-for="column in columns" v-bind:key="column">{{ column }}</option>
                    </select>
                </div>
            </div>
        </div>
        <div v-else-if="action == 'query'">
            <slot />
        </div>
    </div>
</template>

<script>
// TODO: Add options.char and options.lower to settings-html
export default {
    props: ['value', 'options', 'columns', 'action', 'type'],
    mounted: function () {
        var vm = this;
        if (vm.options.from !== '') {
            vm.$globals.$on('formfield-input', function (column, value, translatable) {
                if (column == vm.options.from) {
                    vm.$emit('input', value);
                }
            });
        }
    },
    methods: {
        slugify: function (value) {
            var vm = this;
            return window.slugify(value, {
                replacement: vm.options.char || '-',
                lower: vm.options.lower || true,
            });
        }
    },
};
</script>