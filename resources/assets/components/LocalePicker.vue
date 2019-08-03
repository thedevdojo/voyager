<template>
    <div class="inline-flex">
        <button v-for="locale in $eventHub.locales" class="bg-blue-500 hover:bg-blue-700 text-white p-1 uppercase" @click="changeLocale(locale)">{{ locale }}</button>
    </div>
</template>

<script>
export default {
    methods: {
        changeLocale: function (locale) {
            this.$eventHub.locale = locale;
        }
    }
};
</script>