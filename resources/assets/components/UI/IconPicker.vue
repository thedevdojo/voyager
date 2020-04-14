<template>
    <div>
        <input type="text" class="voyager-input w-full mb-3" :placeholder="__('voyager::generic.search_icons')" v-model="query" />
        <div class="w-full flex flex-wrap">
            <div class="w-1/12 content-center" v-for="icon in filteredIcons.slice(start, end)" :key="'icon-' + icon">
                <button class="button blue" @dblclick="selectIcon(icon)">
                    <icon class="m-1" :icon="icon" :size="8" />
                </button>
            </div>
        </div>
        <div class="button-group mt-2">
            <button
                class="button blue"
                :class="page == (i - 1) ? 'active' : ''"
                v-for="i in pages"
                @click="page = (i - 1)"
                :key="'page-button-'+i">
                {{ i }}
            </button>
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
            page: 0,
            resultsPerPage: 120,
        };
    },
    methods: {
        selectIcon: function (icon) {
            this.$emit('select', icon);
        }
    },
    computed: {
        start: function () {
            return this.page * this.resultsPerPage;
        },
        end: function () {
            return this.start + this.resultsPerPage;
        },
        pages: function () {
            return Math.ceil(this.filteredIcons.length / this.resultsPerPage);
        },
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
    watch: {
        query: function (q) {
            this.page = 0;
        }
    }
};
</script>