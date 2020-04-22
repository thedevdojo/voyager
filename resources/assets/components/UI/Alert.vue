<template>
    <collapse-transition>
        <div class="alert" :class="`border-${color}-500`" v-if="show">
            <div class="flex">
                <div class="flex-shrink-0" v-if="icon">
                    <icon :icon="icon" :size="5" :class="`text-${color}-500`" />
                </div>
                <div :class="[icon ? 'ml-3' : '']">
                    <h3 class="text-sm leading-5 font-medium" :class="`text-${color}-500`" v-if="$slots.title">
                        <slot name="title"></slot>
                    </h3>
                    <div class="text-sm leading-5" :class="[`text-${color}-600`, {'mt-2': $slots.title}]">
                        <slot></slot>
                    </div>
                </div>
                <div class="ml-auto pl-3" v-if="closebutton">
                    <div class="-mx-1 -my-1">
                        <button class="button border-none shadow-none" :class="`text-${color}-500`" @click="$emit('close'); show = false">
                            <icon icon="times" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </collapse-transition>
</template>
<script>
export default {
    props: {
        color: {
            type: String,
            default: 'green'
        },
        icon: {
            default: 'comment-info'
        },
        closebutton: {
            type: Boolean,
            default: true
        },
    },
    data: function () {
        return {
            show: true,
        };
    }
};
</script>

<style scoped>
.alert {
    @apply rounded-md p-4 border;
}
</style>