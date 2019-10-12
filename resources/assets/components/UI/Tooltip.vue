<template>
    <popper trigger="hover" :options="{ placement: 'right' }">
        <span class="popper text-gray-100">
            {{ text }}
        </span>

        <span slot="reference">
            <slot />
        </span>  
    </popper>
</template>

<script>
export default {
    props: ['text'],
    data: function () {
        return {
            
        };
    },
    methods: {
        
    },
};
</script>