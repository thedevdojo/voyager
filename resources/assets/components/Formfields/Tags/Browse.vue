<template>
    <div>
        <slot v-if="show == 'query'"></slot>
        <div v-else>
            <div class="inline-flex items-center">
                <badge :color="options.color || 'blue'" v-for="(tag, i) in tags.slice(0, (options.display || 3))" :key="'tag-'+i">
                    {{ tag }}
                </badge>
                <span class="text-xs ml-1 italic" v-if="tags.length > (options.display || 3)">
                    {{ __('voyager::generic.more_results', { num: (tags.length - (options.display || 3)) }) }}
                </span>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['show', 'options', 'value', 'translatable'],
    data: function () {
        return {
            tags: [],
        };
    },
    mounted: function () {
        this.parseValue();
    },
    methods: {
        parseValue: function () {
            if (this.isArray(this.value)) {
                this.tags = this.value;
            } else {
                try {
                    var json = JSON.parse(this.value);

                    this.tags = json;
                } catch (e) { }
            }
        }
    },
    watch: {
        value: function (value) {
            this.parseValue();
        }
    }
};
</script>