<template>
    <div>
        <input type="text" class="voyager-input w-full mb-3" :placeholder="__('voyager::generic.search_icons')" v-model="query" />
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-1" v-for="icon in filteredIcons" :key="'icon-' + icon">
                <button class="button blue" @dblclick="selectIcon(icon)">
                    <icon class="fill-current m-2 text-white" :icon="icon" :size="8" />
                </button>
            </div>
        </div>
    </div>
</template>
<script>
import * as iconsobj from 'vue-unicons/src/icons';
let icons = new Object(iconsobj);

export default {
    data: function () {
        return {
            query: '',
        };
    },
    methods: {
        selectIcon: function (icon) {
            this.$emit('select', icon);
        }
    },
    computed: {
        filteredIcons: function () {
            var q = this.query.toLowerCase();
            return this.icons.filter(function (icon) {
                return icon.toLowerCase().includes(q);
            });
        },
        icons: function () {
            return Object.keys(icons);
        }
    },
};
</script>