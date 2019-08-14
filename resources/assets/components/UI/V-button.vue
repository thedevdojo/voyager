<template>
    <button class="v-button" ref="button" @click="handleClick" v-bind:disabled="buttonDisabled" :class="type">
        <i class="el-icon-loading" v-if="loading"></i>
        <span v-if="$slots.default">
            <slot></slot>
        </span>
    </button>
</template>

<script>
export default {
    props: {
        type: {
            type: String,
            default: 'default'
        }, 
        loading: false,
        disabled: false
    },
    methods: {
        handleClick(evt) {
            this.$emit('click', evt);
        }
    },
    computed: {
        buttonDisabled() {
            return this.disabled || this.loading;
        }
    }
};
</script>

<style lang="scss" scoped>
    @import 'V-button.scss';
</style>