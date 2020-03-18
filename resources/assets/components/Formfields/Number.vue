<template>
    <div>
        <div v-if="action == 'browse' || action == 'read'">
            {{ value }}
        </div>
        <div v-else-if="action == 'edit' || action == 'add'" class="flex mb-4">
            <div class="w-full m-1">
                <label class="label bigger">{{ translate(options.title, true) }}</label>
                <input
                    type="number"
                    :disabled="options.disabled || false"
                    :placeholder="translate(options.placeholder, true)"
                    :min="options.min || false"
                    :max="options.max || false"
                    :step="options.step || false"
                    v-bind:value="value"
                    v-on:input="$emit('input', $event.target.value)"
                    class="voyager-input" />
                <p class="description">{{ translate(options.description, true) }}</p>
            </div>
        </div>
        <div v-else-if="action == 'mockup'" class="flex mb-4">
            <div class="w-full m-1">
                <label class="label bigger">{{ translate(options.title) }}</label>
                <input
                    type="number"
                    :disabled="action == 'mockup' || options.disabled"
                    :placeholder="translate(options.placeholder)"
                    :value="translate(options.default_value)"
                    class="voyager-input" />
                <p class="description">{{ translate(options.description) }}</p>
            </div>
        </div>
        <div v-else-if="action == 'options'">
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="label text-gray-100">{{ __('voyager::generic.placeholder') }}</label>
                    <language-input
                        class="voyager-input"
                        type="text" :placeholder="__('voyager::generic.placeholder')"
                        v-bind:value="options.placeholder"
                        v-on:input="options.placeholder = $event" />
                </div>
            </div>
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="label text-gray-100">{{ __('voyager::generic.default_value') }}</label>
                    <language-input
                        class="voyager-input"
                        type="number" :placeholder="__('voyager::generic.default_value')"
                        v-model="options.default_value" />
                </div>
            </div>
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="label text-gray-100">{{ __('voyager::generic.min') }}</label>
                    <input
                        class="voyager-input"
                        type="number" :placeholder="__('voyager::generic.min')"
                        v-model="options.min">
                </div>
            </div>
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="label text-gray-100">{{ __('voyager::generic.max') }}</label>
                    <input
                        class="voyager-input"
                        type="number" :placeholder="__('voyager::generic.max')"
                        v-model="options.max">
                </div>
            </div>
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="label text-gray-100">{{ __('voyager::generic.step') }}</label>
                    <input
                        class="voyager-input"
                        type="number" :placeholder="__('voyager::generic.step')"
                        v-model="options.step">
                </div>
            </div>
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="label text-gray-100">{{ __('voyager::generic.disabled') }}</label>
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
    props: ['value', 'options', 'columns', 'action', 'type'],
};
</script>