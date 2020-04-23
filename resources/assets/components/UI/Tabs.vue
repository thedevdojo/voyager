<template>
    <card class="tabs" :show-header="false">
        <div class="sm:hidden">
            <select class="voyager-input" @change="openByIndex($event.target.value)">
                <option v-for="(tab, i) in tabs" :key="'option-'+i" :value="i" class="capitalize">
                    {{ tab.title }}
                </option>
            </select>
        </div>
        <div class="hidden sm:block">
            <div class="border-b wrapper">
                <nav class="-mb-px flex">
                    <a href="#" @click.prevent="openByIndex(i)" class="tab" v-for="(tab, i) in tabs" :key="'tab-'+i" :class="[i > 0 ? 'ml-8' : '', currentTab == i ? 'active' : '']">
                        {{ tab.title }}
                    </a>
                </nav>
            </div>
        </div>
        <div class="content">
            <slide-x-right-transition :duration="50" group>
                <div v-for="(tab, i) in tabs" :key="'slot-'+i" v-if="currentTab == i">
                    <slot :name="tab.name" />
                </div>
            </slide-x-right-transition>
        </div>
    </card>
</template>
<script>
export default {
    props: {
        openTab: {
            type: String,
            default: null,
        },
        tabs: {
            type: Array,
            default: function () {
                return []
            }
        }
    },
    data: function () {
        return {
            currentTab: 0,
        };
    },
    methods: {
        openByIndex: function (index) {
            this.$emit('select', index);
            this.currentTab = index;
        },
        openByName: function (name) {
            var vm = this;
            vm.tabs.forEach(function (tab, i) {
                if (tab.name == name) {
                    vm.currentTab = i;
                }
            });
        }
    },
    computed: {
        current: function () {
            return this.tabs[this.currentTab];
        },
    },
    mounted: function () {
        if (this.openTab !== null) {
            this.openByName(this.openTab);
        }
    },
};
</script>

<style lang="scss" scoped>
.tabs {
    .tab {
        @apply whitespace-no-wrap py-4 px-1 border-b-2 border-transparent font-medium text-sm leading-5 capitalize;
    }

    .content {
        @apply mt-4;
    }
}
</style>