<template>
    <div class="button-group" v-if="$language.locales.length > 1">
        <button
            v-for="locale in $language.locales"
            v-bind:key="locale"
            :class="'button bg-primary px-2 uppercase ' + ($language.locale == locale ? 'active' : '')"
            @click="changeLocale(locale)">
            {{ locale }}
        </button>
    </div>
</template>

<script>
export default {
    methods: {
        changeLocale: function (locale) {
            this.$language.locale = locale;
        }
    }
};
</script>