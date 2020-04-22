<template>
    <div>
        <h6 v-if="translate(options.label, true) !== ''">{{ translate(options.label, true) }}</h6>
        {{ formattedNumber }}
        <p class="description" v-if="translate(options.description, true) !== ''">
            {{ translate(options.description, true) }}
        </p>
    </div>
</template>

<script>
export default {
    props: ['options', 'data', 'translatable'],
    computed: {
        formattedNumber: function () {
            var value = this.translate(this.value, !this.translatable);
            value = this.number_format(value, this.options.decimals, this.options.dec_point, this.options.thousands_sep);
            return value;
        }
    }
};
</script>