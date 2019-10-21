<template>
    <div>
        <div v-if="action == 'browse' || action == 'read'">
            <swatches
                class="bg-transparent"
                :disabled="true"
                background-color="transparent"
                v-bind:value="value"></swatches>
        </div>
        <div v-else-if="action == 'edit' || action == 'add'" class="flex mb-4">
            <div class="w-full m-1">
                <label class="voyager-label">{{ translate(options.title, true) }}</label>
                <div class="content-center text-center">
                    <swatches
                        :colors="options.colors"
                        :disabled="options.disabled || false"
                        show-fallback
                        inline
                        background-color="transparent"
                        v-bind:value="value"
                        v-on:input="$emit('input', $event)"></swatches>
                </div>
                <p>{{ translate(options.description, true) }}</p>
            </div>
        </div>
        <div v-else-if="action == 'mockup'" class="flex mb-4">
            <div class="w-full m-1">
                <label class="voyager-label">{{ translate(options.title) }}</label>
                <div class="content-center text-center">
                    <swatches
                        :colors="options.colors"
                        :value="translate(options.default_value)"
                        background-color="transparent"
                        show-fallback
                        inline
                        :disabled="action == 'mockup' || options.disabled" />
                </div>
                <p>{{ translate(options.description) }}</p>
            </div>
        </div>
        <div v-else-if="action == 'options'">
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">Colors</label>
                    <select class="voyager-input" v-model="options.colors">
                        <option value="basic">Basic</option>
                        <option value="text-basic">Text Basic</option>
                        <option value="material-basic">Material Basic</option>
                        <option value="material-light">Material Light</option>
                        <option value="material-dark">Material Dark</option>
                    </select>
                </div>
            </div>
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::generic.default_value') }}</label>
                    <swatches v-model="options.default_value" :colors="options.colors" inline background-color="transparent" />
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
            <swatches
                v-bind:value="value"
                v-on:input="$emit('input', $event)"
                fallbackInputClass="voyager-input small"
                fallbackOkClass="button blue small"
                fallbackInputType="color"
                show-fallback />
        </div>
    </div>
</template>

<script>
export default {
    props: ['value', 'options', 'columns', 'action', 'type'],
};
</script>