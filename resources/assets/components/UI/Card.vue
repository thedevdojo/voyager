<template>
<div class="card">
    <div class="header" :class="[{'cursor-pointer': headerPointer}]" v-if="showHeader" @click="$emit('click-header')">
        <div class="flex items-center justify-between flex-wrap sm:flex-no-wrap">
            <div class="inline-flex items-center" v-if="!$slots.title">
                <icon v-if="icon" :icon="icon" :size="iconSize" class="ltr:mr-2 rtl:ml-2"></icon>
                <h6 class="leading-6 font-medium">
                    {{ title }}
                </h6>
                <p class="mt-1 text-sm leading-5 text-gray-500">
                    {{ description }}
                </p>
            </div>
            <slot v-else name="title" />
            <div class="flex-shrink-0" v-if="$slots.actions">
                <slot name="actions"></slot>
            </div>
        </div>
    </div>
    <div class="content">
        <slot></slot>
    </div>
    <div class="footer" v-if="$slots.footer">
        <slot name="footer"></slot>
    </div>
</div>
</template>
<script>
export default {
    props: {
        showHeader: {
            type: Boolean,
            default: true,
        },
        headerPointer: {
            type: Boolean,
            default: false,
        },
        title: {
            type: String,
            default: '',
        },
        icon: {
            type: String,
            default: null
        },
        iconSize: {
            type: Number,
            default: 6
        },
        description: {
            type: String,
            default: '',
        },
    }
};
</script>

<style lang="scss" scoped>
.card {
    @apply overflow-hidden shadow border rounded-lg p-4 mb-4 mx-1;

    .header {
        @apply p-2;
    }

    .content {
        @apply px-2;
    }

    .footer {
        @apply p-2;
    }
}
</style>