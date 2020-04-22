<template>
    <div>
        <slot v-if="show == 'query'"></slot>
        <div v-else>
            {{ formattedNumber }}
        </div>
    </div>
</template>

<script>
export default {
    props: ['show', 'options', 'value', 'translatable'],
    computed: {
        formattedNumber: function () {
            var value = this.translate(this.value, !this.translatable);
            value = this.number_format(value, this.options.decimals, this.options.dec_point, this.options.thousands_sep);
            return value;
        }
    }
};
</script>