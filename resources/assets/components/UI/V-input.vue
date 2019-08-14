<template>
    <div class="v-input-container">
        <input class="v-input" v-bind:class="{ filled: filled }" ref="input" :type="type" :name="name" :id="id" v-on:keyup="keyUp" v-model="value">
        <label class="v-label" for="name">{{ placeholder }}</label>
    </div>
</template>

<script>
export default {
    props: ['type', 'name', 'id', 'autofocus', 'placeholder'],
    data: function() {
        return {
            filled : false,
            value: ''
        }
    },
    mounted: function(){
        if( typeof this.autofocus !== "undefined" ) {
            this.$refs.input.autofocus = true;
        }
    },
    methods: {
        keyUp: function () {
            this.filled = (this.value == "") ? false: true;
        }
    },
};
</script>

<style lang="scss" scoped>
    @import 'V-input.scss';
</style>