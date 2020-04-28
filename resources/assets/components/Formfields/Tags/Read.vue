<template>
    <div>
        <h6 v-if="translate(options.label, true) !== ''">{{ translate(options.label, true) }}</h6>
        <badge color="blue" v-for="(tag, i) in tags" :key="'tag-'+i">
            {{ tag }}
        </badge>
        <p class="description" v-if="translate(options.description, true) !== ''">
            {{ translate(options.description, true) }}
        </p>
    </div>
</template>

<script>
export default {
    props: ['options', 'data', 'translatable'],
    data: function () {
        return {
            tags: [],
        };
    },
    mounted: function () {
        if (this.isArray(this.data)) {
            this.tags = this.data;
        }
    }
};
</script>