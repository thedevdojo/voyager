<template>
    <div>
        <div v-if="action == 'edit' || action == 'add' || action == 'read'" class="flex mb-4">
            <div class="w-full m-1">
                <label class="voyager-label">{{ translate(options.title, true) }}</label>
                <div v-for="(value, i) in repeaterValue" class="w-full flex flex-wrap" :key="i">
                    <span class="absolute cursor-pointer" @click="deleteElement(i)" v-if="action !== 'read'">X</span>
                    <div v-for="(formfield, b) in options.formfields" v-bind:key="'formfield-'+b" :class="'w-'+formfield.options.width+' voyager-card'">
                        <div class="body">
                            <component
                                :is="'formfield-'+formfield.type"
                                v-bind:value="data(formfield.column, i, null)"
                                v-on:input="data(formfield.column, i, $event)"
                                :primary="null"
                                :options="formfield.options"
                                :column="formfield.column"
                                :action="action" />
                        </div>
                    </div>
                    <hr v-if="repeaterValue.length > 1 && (i+1) != repeaterValue.length" class="w-full" />
                </div>
                <button class="button blue opacity-75 w-full" @click="addElement" v-if="action !== 'read'">
                    {{ translate(options.add_text, true) }}
                </button>
                <p>{{ translate(options.description, true) }}</p>
            </div>
        </div>
        <div v-else-if="action == 'mockup'" class="flex mb-4">
            <div class="w-full m-1">
                <label class="voyager-label">{{ translate(options.title) }}</label>
                <bread-view-builder
                    v-bind:layout="layout"
                    :columns="columns"
                    :computed="computed"
                    :relationships="relationships"
                    :repeater="true"
                    class="border-dashed border border-gray-500"
                    style="min-height: 150px" />
                <p>{{ translate(options.description) }}</p>
            </div>
        </div>
        <div v-else-if="action == 'options'">
            <div class="flex mb-4" v-if="type == 'view'">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::bread.formfields.repeater.add_element_text') }}</label>
                    <language-input
                        class="voyager-input"
                        type="text" :placeholder="__('voyager::bread.formfields.repeater.add_element_text')"
                        v-bind:value="options.add_text"
                        v-on:input="options.add_text = $event" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['value', 'options', 'columns', 'computed', 'relationships', 'action', 'type'],
    data: function () {
        return {
            layout: {
                formfields: this.options.formfields
            },
            repeaterValue: this.value,
        }
    },
    methods: {
        deleteFormfield: function (id) {
            var vm = this;
            vm.$snotify.confirm(vm.__('voyager::manager.delete_formfield_confirm'), vm.__('voyager::manager.delete_formfield'), {
                timeout: 5000,
                showProgressBar: true,
                closeOnClick: false,
                pauseOnHover: true,
                buttons: [
                    {
                        text: vm.__('voyager::generic.yes'),
                        action: (toast) => {
                            vm.options.formfields.splice(id, 1);
                            vm.$snotify.remove(toast.id);
                            vm.$forceUpdate();
                        }
                    }, {
                        text: vm.__('voyager::generic.no'),
                        action: (toast) => {
                            vm.$snotify.remove(toast.id);
                        }
                    },
                ]
            });
        },
        data: function (key, num, value = null) {
            if (!value) {
                return this.repeaterValue[num][key] || '';
            } else {
                Vue.set(this.repeaterValue[num], key, value);
                this.$emit('input', this.repeaterValue);
            }

            return '';
        },
        addElement: function () {
            var element = {};
            this.options.formfields.forEach(function (formfield) {
                Vue.set(element, formfield.column, '');
            });
            this.repeaterValue.push(element);
        },
        deleteElement: function (num) {
            this.repeaterValue.splice(num, 1);
        }
    },
    mounted: function () {
        if (!this.isArray(this.repeaterValue)) {
            var data = [];
            try {
                data = JSON.parse(this.repeaterValue);
            } catch (e) {
                
            }
            Vue.set(this, 'repeaterValue', data);
            this.$emit('input', this.repeaterValue);
        }
    },
    watch: {
        'layout.formfields': function (formfields) {
            Vue.set(this.options, 'formfields', formfields);
        }
    }
};
</script>