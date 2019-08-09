<template>
    <draggable v-model="layout.formfields" handle=".drag-handle" class="flex flex-wrap bg-gray-300 w-full">
        <formfield-mockup
            v-for="(formfield, i) in layout.formfields"
            :key="i"
            :ref="'formfield-'+i"
            :formfield="formfield"
            :fields="fields"
            :computed="computed"
            :relationships="relationships"
            action="mockup"
            type="view"
        />
    </draggable>
</template>

<script>
export default {
    props: ['layout', 'fields', 'computed', 'relationships'],
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
                e.preventDefault();
                var rect = vm.$el.getBoundingClientRect();
                var x = e.clientX - rect.left;
                var threshold = rect.width / 12;
                var size = Math.min(Math.max(Math.round(x / threshold), 0), 12);
                Vue.set(vm.layout.formfields[vm.currentResizeId].options, 'width', vm.widthClasses[size]);
            }
        });
    }
};
</script>