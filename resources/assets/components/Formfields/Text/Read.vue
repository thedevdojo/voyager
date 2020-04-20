<template>
    <div>
        <h6 v-if="translate(options.label) !== ''">{{ translate(options.label) }}</h6>
        {{ translate(data) }}
        <p class="description" v-if="translate(options.description) !== ''">
            {{ translate(options.description) }}
        </p>
    </div>
</template>

<script>
export default {
    props: ['options', 'data'],
};
</script>