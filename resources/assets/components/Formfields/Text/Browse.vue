<template>
    <div>
        <slot v-if="show == 'query'"></slot>
        <div v-else>
            <div>
                <span v-if="options.display_length > 0">
                    {{ translate(value, !translatable).slice(0, options.display_length) }}
                </span>
                <span v-else>
                    {{ translate(value, !translatable) }}
                </span>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['show', 'options', 'value', 'translatable'],
};
</script>