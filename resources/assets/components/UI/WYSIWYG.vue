<template>
    <div class="wysiwyg">
        <editor-menu-bar :editor="editor" v-slot="{ commands, isActive }">
            <div class="menubar">
                <button :class="{ 'is-active': isActive.bold() }" @click="commands.bold" >
                    <icon icon="bold"></icon>
                </button>
                <button class="menubar__button" :class="{ 'is-active': isActive.italic() }" @click="commands.italic">
                    <icon icon="italic"></icon>
                </button>
                <button class="menubar__button" :class="{ 'is-active': isActive.strike() }" @click="commands.strike">
                    <icon icon="text-strike-through"></icon>
                </button>
                <button class="menubar__button" :class="{ 'is-active': isActive.underline() }" @click="commands.underline">
                    <icon icon="underline"></icon>
                </button>
                <button class="menubar__button" :class="{ 'is-active': isActive.code() }" @click="commands.code">
                    <icon icon="arrow"></icon>
                </button>
                <button class="menubar__button" :class="{ 'is-active': isActive.paragraph() }" @click="commands.paragraph">
                    <icon icon="paragraph"></icon>
                </button>
                <button class="menubar__button" :class="{ 'is-active': isActive.heading({ level: 1 }) }" @click="commands.heading({ level: 1 })">
                    H1
                </button>
                <button class="menubar__button" :class="{ 'is-active': isActive.heading({ level: 2 }) }" @click="commands.heading({ level: 2 })">
                    H2
                </button>
                <button class="menubar__button" :class="{ 'is-active': isActive.heading({ level: 3 }) }" @click="commands.heading({ level: 3 })">
                    H3
                </button>
                <button class="menubar__button" :class="{ 'is-active': isActive.bullet_list() }" @click="commands.bullet_list">
                    <icon icon="list-ul"></icon>
                </button>
                <button class="menubar__button" :class="{ 'is-active': isActive.ordered_list() }" @click="commands.ordered_list">
                    <icon icon="list-ul"></icon>
                </button>
                <button class="menubar__button" :class="{ 'is-active': isActive.blockquote() }" @click="commands.blockquote">
                    BQ
                </button>
                <button class="menubar__button" :class="{ 'is-active': isActive.code_block() }" @click="commands.code_block">
                    CB
                </button>
                <button class="menubar__button" @click="commands.horizontal_rule">HR</button>
                <button class="menubar__button" @click="commands.undo">Undo</button>
                <button class="menubar__button" @click="commands.redo">Redo</button>
            </div>
        </editor-menu-bar>

        <editor-content class="content voyager-input" spellcheck="false" :editor="editor" />
    </div>
</template>
<script>
// https://github.com/scrumpy/tiptap
import { Editor, EditorContent, EditorMenuBar } from "tiptap";
import {
    Blockquote,
    CodeBlock,
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
    Link,
    Strike,
    Underline,
    History
} from "tiptap-extensions";

export default {
  props: ["value"],
  components: {
    EditorContent,
    EditorMenuBar
  },
  data: function() {
    return {
      editor: new Editor({
        extensions: [
          new Blockquote(),
          new BulletList(),
          new CodeBlock(),
          new HardBreak(),
          new Heading({ levels: [1, 2, 3] }),
          new HorizontalRule(),
          new ListItem(),
          new OrderedList(),
          new TodoItem(),
          new TodoList(),
          new Link(),
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
  methods: {},
  computed: {},
  beforeDestroy() {
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