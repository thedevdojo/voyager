<template>
    <div class="wysiwyg">
        <editor-menu-bar :editor="editor" v-slot="{ commands, isActive }">
            <div class="button-group">
                <button class="button small icon-only" :class="[isActive.bold() ? 'blue' : 'dark-gray']" @click="commands.bold" v-tooltip="__('voyager::wysiwyg.bold')">
                    <icon icon="bold" :size="6"></icon>
                </button>
                <button class="button small icon-only" :class="[isActive.italic() ? 'blue' : 'dark-gray']" @click="commands.italic" v-tooltip="__('voyager::wysiwyg.italic')">
                    <icon icon="italic" :size="6"></icon>
                </button>
                <button class="button small icon-only" :class="[isActive.strike() ? 'blue' : 'dark-gray']" @click="commands.strike" v-tooltip="__('voyager::wysiwyg.strike')">
                    <icon icon="text-strike-through" :size="6"></icon>
                </button>
                <button class="button small icon-only" :class="[isActive.underline() ? 'blue' : 'dark-gray']" @click="commands.underline" v-tooltip="__('voyager::wysiwyg.underline')">
                    <icon icon="underline" :size="6"></icon>
                </button>
                <div class="divider"></div>
                <button class="button small icon-only" :class="[isActive.code() ? 'blue' : 'dark-gray']" @click="commands.code" v-tooltip="__('voyager::wysiwyg.code')">
                    <icon icon="arrow" :size="6"></icon>
                </button>
                <button class="button small icon-only" :class="[isActive.paragraph() ? 'blue' : 'dark-gray']" @click="commands.paragraph" v-tooltip="__('voyager::wysiwyg.paragraph')">
                    <icon icon="paragraph" :size="6"></icon>
                </button>
                <button class="button small icon-only" :class="[isActive.bullet_list() ? 'blue' : 'dark-gray']" @click="commands.bullet_list" v-tooltip="__('voyager::wysiwyg.bullet_list')">
                    <icon icon="list-ul" :size="6"></icon>
                </button>
                <button class="button small icon-only" :class="[isActive.ordered_list() ? 'blue' : 'dark-gray']" @click="commands.ordered_list" v-tooltip="__('voyager::wysiwyg.ordered_list')">
                    <icon icon="list-ul" :size="6"></icon>
                </button>
                <button class="button small icon-only" :class="[isActive.horizontal_rule() ? 'blue' : 'dark-gray']" @click="commands.horizontal_rule" v-tooltip="__('voyager::wysiwyg.horizontal_rule')">
                    HR
                </button>
                <div class="divider"></div>
                <button class="button small icon-only" :class="[isActive.heading({ level: 1 }) ? 'blue' : 'dark-gray']" @click="commands.heading({ level: 1 })" v-tooltip="__('voyager::wysiwyg.heading_1')">
                    H1
                </button>
                <button class="button small icon-only" :class="[isActive.heading({ level: 2 }) ? 'blue' : 'dark-gray']" @click="commands.heading({ level: 2 })" v-tooltip="__('voyager::wysiwyg.heading_2')">
                    H2
                </button>
                <button class="button small icon-only" :class="[isActive.heading({ level: 3 }) ? 'blue' : 'dark-gray']" @click="commands.heading({ level: 3 })" v-tooltip="__('voyager::wysiwyg.heading_3')">
                    H3
                </button>
                <div class="divider"></div>
                <button class="button small icon-only dark-gray" @click="commands.undo" v-tooltip="__('voyager::wysiwyg.undo')">
                    <icon icon="redo" mirrored :size="6"></icon>
                </button>
                <button class="button small icon-only dark-gray" @click="commands.redo" v-tooltip="__('voyager::wysiwyg.redo')">
                    <icon icon="redo" :size="6"></icon>
                </button>
            </div>
        </editor-menu-bar>

        <editor-content class="content voyager-input" spellcheck="false" :editor="editor" />
    </div>
</template>
<script>
// https://github.com/scrumpy/tiptap
import { Editor, EditorContent, EditorMenuBar } from "tiptap";
import {
    HardBreak,
    Heading,
    HorizontalRule,
    OrderedList,
    BulletList,
    ListItem,
    TodoItem,
    TodoList,
    Bold,
    Code,
    Italic,
    Strike,
    Underline,
    History
} from "tiptap-extensions";

export default {
    props: ['value'],
    components: {
        EditorContent,
        EditorMenuBar
    },
    data: function() {
        return {
            editor: new Editor({
                extensions: [
                    new BulletList(),
                    new HardBreak(),
                    new Heading({ levels: [1, 2, 3] }),
                    new HorizontalRule(),
                    new ListItem(),
                    new OrderedList(),
                    new TodoItem(),
                    new TodoList(),
                    new Bold(),
                    new Code(),
                    new Italic(),
                    new Strike(),
                    new Underline(),
                    new History()
                ],
                content: this.$store.ui.lorem
            })
        };
    },
    methods: {

    },
    computed: {

    },
    beforeDestroy: function () {
        this.editor.destroy();
    }
};
</script>

<style lang="scss" scoped>
.wysiwyg {
    
    .content {
        @apply min-h-64;
    }
}
</style>

<style lang="scss">
.ProseMirror {
    @apply h-full w-full;
    &:focus {
        outline: none !important;
    }
}
</style>