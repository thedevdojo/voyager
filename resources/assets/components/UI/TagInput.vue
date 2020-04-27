<template>
    <div class="voyager-input taginput" @click="$refs.input.focus()">
        <sort-container v-model="tags" tag="span" lockAxis="x" axis="x" :hideSortableGhost="false">
            <sort-element v-for="(tag, i) in tags" :key="'tag-'+i" :index="i" tag="span" :disabled="!allowReorder">
                <badge :color="badgeColor" icon="times" @click-icon="removeTag(tag)" class="large" :class="[allowReorder ? 'cursor-move' : '']">
                    {{ tag }}
                </badge>
            </sort-element>
        </sort-container>
        <input type="text" class="" ref="input" v-on:keyup.enter="addTag">
    </div>
</template>
<script>
export default {
    props: {
        value: {
            type: Array,
            default: function () {
                return [];
            }
        },
        badgeColor: {
            type: String,
            default: 'blue',
        },
        allowEmpty: {
            type: Boolean,
            default: false,
        },
        allowReorder: {
            type: Boolean,
            default: true,
        },
        min: {
            type: Number,
            default: 0,
        },
        max: {
            type: Number,
            default: 0,
        }
    },
    data: function () {
        return {
            tags: this.value,
        };
    },
    methods: {
        addTag: function (e) {
            if (this.max > 0 && this.tags.length >= this.max) {
                return;
            }
            var content = e.target.value;
            if (!this.allowEmpty && content == '') {
                return;
            }
            this.tags.push(content);
            e.target.value = '';
        },
        removeTag: function (tag) {
            if (this.tags.length <= this.min) {
                return;
            }
            this.tags.splice(this.tags.indexOf(tag), 1);
        }
    },
    watch: {
        tags: function (tags) {
            this.$emit('input', tags);
        }
    }
};
</script>

<style lang="scss" scoped>
.taginput {
    input {
        @apply bg-transparent border-0;

        &:focus {
            @apply outline-none;
        }
    }
}
</style>