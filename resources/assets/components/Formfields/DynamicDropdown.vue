<template>
    <div>
        <div v-if="action == 'browse' || action == 'read'">
            {{ value }}
        </div>
        <div v-else-if="action == 'edit' || action == 'add'" class="flex mb-4">
            <div class="w-full m-1">
                <label class="voyager-label">{{ translate(options.title, true) }}</label>
                <div class="flex">
                    <select
                        class="voyager-input"
                        v-for="(single_options, i) in dynamic_options"
                        :key="i"
                        v-model="selectedOptions[i]"
                        @change="getOptions($event.target.value, (i+1))"
                        :size="options.size">
                        <option v-for="(option, key) in single_options" :key="key" v-bind:value="options.store == 'key' ? key : option">
                            {{ option }}
                        </option>
                    </select>
                </div>
                <p>{{ translate(options.description, true) }}</p>
            </div>
        </div>
        <div v-else-if="action == 'mockup'" class="flex mb-4">
            <div class="w-full m-1">
                <label class="voyager-label">{{ translate(options.title) }}</label>
                <select class="voyager-input" disabled></select>
                <p>{{ translate(options.description) }}</p>
            </div>
        </div>
        <div v-else-if="action == 'options'">
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::generic.controller') }}</label>
                    <input type="text" v-model="options.controller" class="voyager-input" />
                </div>
            </div>
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::generic.method') }}</label>
                    <input type="text" v-model="options.method" class="voyager-input" />
                </div>
            </div>
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::generic.save') }}</label>
                    <select class="voyager-input" v-model="options.store">
                        <option value="key">{{ __('voyager::generic.key') }}</option>
                        <option value="value">{{ __('voyager::generic.value') }}</option>
                    </select>
                </div>
            </div>
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::generic.size') }}</label>
                    <input type="number" v-model="options.size" class="voyager-input" min="1" />
                </div>
            </div>
        </div>
        <div v-else-if="action == 'query'">
            <slot />
        </div>
    </div>
</template>

<script>
export default {
    props: ['value', 'options', 'columns', 'action', 'type'],
    data: function () {
        return {
            selectedOptions: [],
            dynamic_options: [],
        };
    },
    methods: {
        getOptions: function (value, num) {
            var vm = this;

            if (value) {
                vm.$emit('input', value);
            }

            vm.selectedOptions.length = num;
            
            for (var i = num; i < vm.dynamic_options.length; i++) {
                vm.dynamic_options[i] = [];
            }

            axios.post(vm.route('voyager.get-options'), {
                controller: vm.options.controller,
                method: vm.options.method,
                selected: vm.selectedOptions,
            })
            .then(function (response) {
                if (vm.isObject(response.data)) {
                    Vue.set(vm.dynamic_options, num, response.data);
                }
            })
            .catch(function (error) {
                vm.$snotify.error(error);
                if (vm.debug) {
                    vm.debug(error.response.data.message, true, 'error');
                }
                vm.loading = false;
            });
        }
    },
    mounted: function () {
        this.getOptions(null, 0);
    }
};
</script>