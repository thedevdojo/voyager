<template>
    <div :class="'fixed '+classes">
        <slot />
    </div>
</template>
<script>
export default {
    props: {
        position: {
            default: 'bottom-right',
        },
    },
    data: function () {
        return {
            
        };
    },
    computed: {
        classes: function () {
            if (this.position == 'top-right') {
                return 'top-0 right-0 mx-8 my-20';
            } else if (this.position == 'top-left') {
                return 'top-0 left-0';
            } else if (this.position == 'bottom-left') {
                return 'bottom-0 left-0';
            }

            return 'bottom-0 right-0 mx-8 my-8';
        }
    },
    methods: {
        click: function () {
            if (this.$slots.default) {

            }
        }
    },
};
</script>