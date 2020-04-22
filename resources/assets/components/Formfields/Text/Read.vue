<template>
    <div>
        <h6 v-if="translate(options.label, true) !== ''">{{ translate(options.label, true) }}</h6>
        {{ translate(data, !translatable) }}
        <p class="description" v-if="translate(options.description, true) !== ''">
            {{ translate(options.description, true) }}
        </p>
    </div>
</template>

<script>
export default {
    props: ['options', 'data', 'translatable'],
};
</script>