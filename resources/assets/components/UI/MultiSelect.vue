<template>
    <div>
        <div class="voyager-input" @click="open" v-click-outside="close">
            <span v-if="selectedOptions.length == 0">&nbsp;</span>
            <div v-if="multiple">
                <badge
                    v-for="(option, i) in selectedOptions"
                    :key="'badge-'+i">
                    {{ option[badgeProp] }} <span class="cursor-pointer" @click.prevent="removeSelected(i)">X</span>
                </badge>
            </div>
            <span v-else-if="selectedOptions[0]">
                {{ selectedOptions[0][badgeProp] }}
            </span>
            <slot>
                <dropdown ref="dropdown">
                    <div slot="opener"></div>
                    <div class="absolute bg-white rounded-lg shadow-xl">
                        <div class="voyager-table striped">
                            <table class="m-0">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" :disabled="!multiple" @click="selectAll">
                                        </th>
                                        <th v-for="(prop, a) in displayProp" :key="'th-'+a">
                                            {{ prop }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(option, i) in options" :key="'option-'+i">
                                        <td>
                                            <input
                                                :type="multiple ? 'checkbox' : 'radio'"
                                                :checked="checked(option[keyProp])"
                                                @input="selectOption(option[keyProp], $event)">
                                        </td>
                                        <td v-for="(prop, b) in displayProp" :key="'td-'+b">
                                            {{ option[prop] }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </dropdown>
            </slot>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        // The available options as an array containing objects
        options: {
            type: Array,
            required: true,
        },
        // The selected options
        selected: {
            type: [Array, String, Number],
            required: false,
        },
        // The props(s) which is/are shown in the dropdown
        displayProp: {
            type: [String, Array],
            required: true,
        },
        // The prop which is shown in the input as a badge
        badgeProp: {
            type: String,
            required: true,
        },
        // The prop that is saved to the select-prop when selected
        keyProp: {
            type: String,
            required: true,
        },
        multiple: {
            type: Boolean,
            default: false,
        },
        min: {
            type: Number,
            default: 0,
        },
        max: {
            type: Number,
            default: 0,
        },
        disabled: {
            type: Boolean,
            default: false,
        }
    },
    data: function () {
        return {
            select: this.selected,
        };
    },
    computed: {
        selectedOptions: function () {
            var vm = this;
            return vm.options.filter(function (option) {
                if (vm.multiple) {
                    return vm.select.includes(option[vm.keyProp]);
                }

                return vm.select == option[vm.keyProp];
            });
        }
    },
    methods: {
        open: function () {
            if (this.$refs.dropdown) {
                this.$refs.dropdown.open();
            } else if (this.$slots.default && this.$slots.default.length > 0) {
                this.$slots.default[0].child.open();
            }
        },
        close: function (e) {
            if (this.$refs.dropdown) {
                this.$refs.dropdown.close();
            } else if (this.$slots.default && this.$slots.default.length > 0) {
                this.$slots.default[0].child.close();
            }
        },
        checked: function (key) {
            if (this.multiple) {
                return this.select.includes(key);
            }
            
            return this.select == key;
        },
        selectOption: function (key, e) {
            var checked = e.target.checked;

            if (!this.multiple) {
                if (checked) {
                    this.select = key;
                } else {
                    this.select = null;
                }
                this.$emit('select', this.select);

                return;
            }
            if (checked && !this.select.includes(key)) {
                this.select.push(key);
            } else if (!checked && this.select.includes(key)) {
                this.select = this.select.filter(function (i) {
                    return i !== key;
                });
            }

            this.$emit('select', this.select);
        },
        removeSelected: function (num) {
            this.select.splice(num, 1);
            this.$emit('select', this.select);
        },
        selectAll: function (e) {
            var checked = e.target.checked;
            var vm = this;
            
            if (checked) {
                vm.select = [];
                vm.options.forEach(function (option) {
                    vm.select.push(option[vm.keyProp]);
                });
            } else {
                vm.select = [];
            }

            vm.$emit('select', this.select);
        }
    },
    watch: {
        selected: function (selected) {
            this.select = selected;
        }
    },
};
</script>