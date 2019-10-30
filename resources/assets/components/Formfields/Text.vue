<template>
    <div>
        <div v-if="action == 'browse'">
            <div v-if="value && value.length > options.max_characters">
                {{ value.substring(0, options.max_characters) }}...
            </div>
            <div v-else>
                {{ value }}
            </div>
        </div>
        <div v-else-if="action == 'read'">
            {{ value }}
        </div>
        <div v-else-if="action == 'edit' || action == 'add'">
            <div class="w-full m-1">
                <label class="voyager-label">{{ translate(options.title, true) }}</label>
                <input
                    v-if="(options.rows || 1) == 1"
                    type="text"
                    :disabled="options.disabled || false"
                    :placeholder="translate(options.placeholder, true)"
                    v-bind:value="value"
                    v-on:input="$emit('input', $event.target.value)"
                    class="voyager-input" />
                <textarea
                    v-else
                    :rows="options.rows"
                    :disabled="options.disabled || false"
                    :placeholder="translate(options.placeholder, true)"
                    v-bind:value="value"
                    v-on:input="$emit('input', $event.target.value)"
                    class="voyager-input">

                </textarea>
                <p>{{ translate(options.description, true) }}</p>
            </div>
        </div>
        <div v-else-if="action == 'mockup'" class="flex mb-4">
            <div class="w-full m-1">
                <label class="voyager-label">{{ translate(options.title) }}</label>
                <input
                    v-if="(options.rows || 1) == 1"
                    type="text"
                    :disabled="action == 'mockup' || options.disabled"
                    :placeholder="translate(options.placeholder)"
                    :value="translate(options.default_value)"
                    class="voyager-input" />
                <textarea
                    v-else
                    :rows="options.rows"
                    :disabled="action == 'mockup' || options.disabled"
                    :placeholder="translate(options.placeholder)"
                    v-bind:value="translate(options.default_value)"
                    class="voyager-input">
                </textarea>
                <p>{{ translate(options.description) }}</p>
            </div>
        </div>
        <div v-else-if="action == 'options'">
            <div class="flex mb-4" v-if="type == 'view'">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::generic.placeholder') }}</label>
                    <language-input
                        class="voyager-input"
                        type="text" :placeholder="__('voyager::generic.placeholder')"
                        v-bind:value="options.placeholder"
                        v-on:input="options.placeholder = $event" />
                </div>
            </div>
            <div class="flex mb-4" v-if="type == 'view'">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::generic.rows') }}</label>
                    <input
                        class="voyager-input"
                        type="number" min="1" max="255" :placeholder="__('voyager::generic.rows')"
                        v-model.number="options.rows" />
                </div>
            </div>
            <div class="flex mb-4" v-if="type == 'view'">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::generic.default_value') }}</label>
                    <language-input
                        class="voyager-input"
                        type="text" :placeholder="__('voyager::generic.default_value')"
                        v-model="options.default_value" />
                </div>
            </div>
            <div class="flex mb-4" v-if="type == 'view'">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::generic.disabled') }}</label>
                    <input type="checkbox" v-model="options.disabled">
                </div>
            </div>
            <div class="flex mb-4" v-if="type == 'list'">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::generic.max_characters') }}</label>
                    <input
                        type="number"
                        class="voyager-input"
                        min="1"
                        :placeholder="__('voyager::generic.max_characters')"
                        v-model="options.max_characters">
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