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
        this.translations = this.get_input_as_translatable_object(this.value);
    },
    computed: {
        currentText: {
            get: function () {
                return this.translations[this.$language.locale];
            },
            set: function (value) {
                Vue.set(this.translations, this.$language.locale, value);
                this.$emit('input', this.translations);
            }
        }
    },
    watch: {
        value: function (val) {
            this.translations = this.get_input_as_translatable_object(this.value);
        }
    }
};
</script>