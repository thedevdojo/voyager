<template>
    <div>
        <div v-if="action == 'browse' || action == 'read'">
            {{ value }}
        </div>
        <div v-else-if="action == 'edit' || action == 'add'" class="flex mb-4">
            <div class="w-full m-1">
                <label class="voyager-label">{{ translate(options.title, true) }}</label>
                <flat-pickr
                    class="voyager-input"
                    :disabled="options.disabled || false"
                    :placeholder="translate(options.placeholder, true)"
                    v-bind:value="value"
                    v-on:input="$emit('input', $event.target.value)"
                    :config="config" />
                <p>{{ translate(options.description, true) }}</p>
            </div>
        </div>
        <div v-else-if="action == 'mockup'" class="flex mb-4">
            <div class="w-full m-1">
                <label class="voyager-label">{{ translate(options.title) }}</label>
                <flat-pickr
                    class="voyager-input"
                    :disabled="action == 'mockup' || options.disabled"
                    :placeholder="translate(options.placeholder, true)"
                    :value="translate(options.default_value)"
                    :config="config" />
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
                        type="number" :placeholder="__('voyager::generic.default_value')"
                        v-model="options.default_value" />
                </div>
            </div>
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::generic.disabled') }}</label>
                    <input type="checkbox" v-model="options.disabled">
                </div>
            </div>
        </div>
        <div v-else-if="action == 'query'">
            <slot />
        </div>
    </div>
</template>

<script>
export default {
    props: ['value', 'options', 'fields', 'action'],
    computed: {
        config: function () {
            return {
                format: 'd-m-Y H:i',
                locale: this.initial_locale,
            };
        }
    }
};
</script>