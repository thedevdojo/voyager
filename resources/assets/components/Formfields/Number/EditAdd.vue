<template>
    <div>
        <label class="label" v-if="translate(options.label, true) !== ''">{{ translate(options.label, true) }}</label>
        <input
            type="number"
            class="voyager-input w-full"
            v-bind:value="value"
            @input="$emit('input', $event.target.value)"
            :min="options.min"
            :max="options.max"
            :step="options.step"
            :placeholder="translate(options.placeholder, true)">
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