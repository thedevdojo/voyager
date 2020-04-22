<template>
    <div>
        <label class="label" v-if="translate(options.label, true) !== ''">{{ translate(options.label, true) }}</label>
        <input
            v-if="options.rows == 1"
            type="text"
            class="voyager-input w-full"
            v-bind:value="value || translate(options.default_value, true)"
            @input="$emit('input', $event.target.value)"
            :placeholder="translate(options.placeholder, true)">
        <textarea
            v-else
            class="voyager-input w-full"
            :rows="options.rows"
            v-bind:value="value || translate(options.default_value)"
            @input="$emit('input', $event.target.value)"
            :placeholder="translate(options.placeholder)"></textarea>
        <p class="description" v-if="translate(options.description, true) !== ''">
            {{ translate(options.description, true) }}
        </p>
    </div>
</template>

<script>
export default {
    props: ['options', 'value'],
};
</script>