<template>
<i>
    <svg xmlns="http://www.w3.org/2000/svg" :viewBox="viewbox" v-html="iconPath" :class="'fill-current h-'+size+' w-' + size" />
</i>
</template>

<script>
import * as iconsobj from 'vue-unicons/src/icons';
let icons = new Object(iconsobj);

export default {
    props: {
        icon: {
            type: String,
            required: true
        },
        size: {
            type: Number,
            default: 6,
        },
        viewbox: {
            type: String,
            default: '0 0 24 24'
        },
    },
    computed: {
        iconPath: function () {
            var name = this.icon;
            if (!name.startsWith('Uni') && !name.startsWith('uni')) {
                name = this.studly(name);
                name = 'uni' + name[0].toUpperCase() + name.slice(1);
            } else {
                name = name[0].toLowerCase() + name.slice(1);
            }
            if (icons.hasOwnProperty(name)) {
                return icons[name].path;
            } else {
                this.debug('Icon "'+name+'" does not exist!', true, 'warn');
            }
        }
    }
};
</script>
