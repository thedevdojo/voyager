<template>
    <div>
        <label class="label" v-if="translate(options.label) !== ''">{{ translate(options.label) }}</label>
        <input
            v-if="options.rows == 1"
            type="text"
            class="voyager-input w-full"
            v-bind:value="value || translate(options.default_value)"
            @input="$emit('input', $event.data)"
            :placeholder="translate(options.placeholder)">
        <textarea
            v-else
            class="voyager-input w-full"
            :rows="options.rows"
            v-bind:value="value || translate(options.default_value)"
            @input="$emit('input', $event.data)"
            :placeholder="translate(options.placeholder)"></textarea>
        <p class="description" v-if="translate(options.description) !== ''">
            {{ translate(options.description) }}
        </p>
    </div>
</template>

<script>
export default {
    props: ['options', 'value'],
};
</script>