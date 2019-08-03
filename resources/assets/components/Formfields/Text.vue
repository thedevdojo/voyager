<template>
    <div>
        <div v-if="action == 'browse'" class="flex mb-4">
            <div class="w-full m-1 text-white">
            </div>
        </div>
        <div v-else-if="action == 'edit' || action == 'add' || action == 'mockup'" class="flex mb-4">
            <div class="w-full m-1">
                <label class="block text-gray-700 text-sm font-bold mb-2">{{ translate(options.title) }}</label>
                <input
                    type="text"
                    :disabled="action == 'mockup' || options.disabled"
                    :placeholder="translate(options.placeholder)"
                    :value="data || translate(options.default_value)"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
                <p>{{ translate(options.description) }}</p>
            </div>
        </div>
        <div v-else-if="action == 'options'">
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="block text-gray-100 text-sm font-bold mb-2">{{ __('voyager::generic.placeholder') }}</label>
                    <language-input
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        type="text" :placeholder="__('voyager::generic.placeholder')"
                        v-bind:value="options.placeholder"
                        v-on:input="options.placeholder = $event" />
                </div>
            </div>
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="block text-gray-100 text-sm font-bold mb-2">{{ __('voyager::generic.default_value') }}</label>
                    <language-input
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        type="text" :placeholder="__('voyager::generic.default_value')"
                        v-model="options.default_value" />
                </div>
            </div>
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="block text-gray-100 text-sm font-bold mb-2">{{ __('voyager::generic.disabled') }}</label>
                    <input type="checkbox" v-model="options.disabled">
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['data', 'options', 'fields', 'action'],
};
</script>