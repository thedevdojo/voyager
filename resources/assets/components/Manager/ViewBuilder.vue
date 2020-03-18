<template>
    <div>
        <draggable
            v-model="layout.formfields"
            handle=".drag-handle"
            class="flex flex-wrap w-full"
            :animation="200"
            ghost-class="ghost"
            :group="{ name: 'draggable', pull: true, put: true }"
            v-click-outside="endFormfieldResize">
            <formfield-mockup
                v-for="(formfield, i) in layout.formfields"
                :key="i"
                :ref="'formfield-'+i"
                :formfield="formfield"
                :columns="columns"
                :computed="computed"
                :relationships="relationships"
                :repeater="repeater"
                action="mockup"
                type="view"
            />
        </draggable>
        <slidein :opened="showSettings" v-on:closed="$emit('layout-settings-closed')">
            <div class="flex mb-4">
                <div class="w-2/3">
                    <h4 class="text-gray-100 text-lg">{{ __('voyager::generic.options') }}</h4>
                </div>
                <div class="w-1/3 text-right text-gray-100">
                    <button @click="$emit('layout-settings-closed')" class="button green">X</button>
                </div>
            </div>
            <div class="flex mb-4">
                <div class="w-1/2 m-1">
                    <label class="label text-gray-100">{{ __('voyager::manager.show_back_button') }}</label>
                    <input type="checkbox" v-model="layout.back_button">
                </div>
                <div class="w-1/2 m-1">
                    <label class="label text-gray-100">{{ __('voyager::manager.show_create_button') }}</label>
                    <input type="checkbox" v-model="layout.create_button">
                </div>
            </div>
        </slidein>
    </div>
</template>

<script>
export default {
    props: ['layout', 'columns', 'computed', 'relationships', 'repeater', 'show-settings'],
    data: function () {
        return {
            currentOptionsId: null,
            currentResizeId: null,
            widthClasses: [
                '1/12',
                '2/12',
                '3/12',
                '4/12',
                '5/12',
                '6/12',
                '7/12',
                '8/12',
                '9/12',
                '10/12',
                '11/12',
                'full',
            ]
        };
    },
    methods: {
        startFormfieldResize: function (id) {
            this.currentResizeId = id;
        },
        endFormfieldResize: function (id) {
            this.currentResizeId = null;
        }
    },
    computed: {
        currentResizingFormfield: function () {
            if (this.currentResizeId !== null) {
                return this.$refs['formfield-'+this.currentResizeId][0];
            }

            return null;
        }
    },
    mounted: function () {
        var vm = this;

        this.$el.addEventListener('mouseup', () => {
            vm.endFormfieldResize();
        });
        this.$el.addEventListener('mouseleave', () => {
            vm.endFormfieldResize();
        });
        this.$el.addEventListener('mousemove', (e) => {
            if (vm.currentResizeId !== null) {
                var sections = vm.widthClasses.length;
                e.preventDefault();
                var rect = vm.$el.getBoundingClientRect();
                var x = e.clientX - rect.left;
                var threshold = rect.width / sections;
                var size = Math.min(Math.max(Math.round(x / threshold), 0), sections);
                Vue.set(vm.layout.formfields[vm.currentResizeId].options, 'width', vm.widthClasses[size]);
            }
        });
    },
};
</script>
<style scoped>
.ghost {
    @apply opacity-50;
}
</style>