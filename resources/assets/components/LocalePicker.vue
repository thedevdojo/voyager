<template>
    <div class="w-full text-right">
        <button
            v-for="locale in $eventHub.locales"
            v-bind:key="locale"
            class="voyager-button blue small uppercase"
            @click="changeLocale(locale)">
            {{ locale }}
        </button>
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