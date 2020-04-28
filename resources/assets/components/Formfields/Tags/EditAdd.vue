<template>
    <div>
        <label class="label" v-if="translate(options.label, true) !== ''">{{ translate(options.label, true) }}</label>
        <tag-input
            :min="options.min"
            :max="options.max"
            :badge-color="options.color || 'blue'"
            v-bind:value="value || []"
            @input="$emit('input', $event)">
        </tag-input>
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