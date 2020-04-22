<template>
    <div class="button-group" v-if="$language.locales.length > 1">
        <button
            v-for="locale in $language.locales"
            v-bind:key="locale"
            class="button blue uppercase"
            :class="[$language.locale == locale ? 'active' : '', small ? 'small' : '']"
            @click="changeLocale(locale)">
            {{ locale }}
        </button>
    </div>
</template>

<script>
export default {
    props: {
        small: {
            type: Boolean,
            default: true,
        },
    },
    methods: {
        changeLocale: function (locale) {
            this.$language.locale = locale;
        }
    }
};
</script>