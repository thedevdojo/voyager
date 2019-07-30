<template>
    <input v-model="currentText">
</template>

<script>
export default {
    props: ['value'],
    data: function () {
        return {
            translations: {}
        };
    },
    mounted: function () {
        this.translations = Vue.prototype.get_input_as_translatable_object(this.value);
    },
    computed: {
        currentText: {
            get: function () {
                return this.translations[this.$eventHub.locale];
            },
            set: function (value) {
                Vue.set(this.translations, this.$eventHub.locale, value);
                this.$emit('input', this.translations);
            }
        }
    },
};
</script>