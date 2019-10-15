<template>
    <div>
        <div v-if="action == 'browse' || action == 'read'">
            {{ value }}
        </div>
        <div v-else-if="action == 'edit' || action == 'add' || action == 'mockup'" class="flex mb-4">
            <div class="w-full m-1">
                <label class="voyager-label">{{ translate(options.title, true) }}</label>
                <div class="flex">
                    <flat-pickr
                        class="voyager-input"
                        :placeholder="translate(options.placeholder, true)"
                        v-bind:value="value"
                        v-on:input="selectedDate = $event; $emit('input', $event)"
                        :config="config" ></flat-pickr>
                </div>
                <p>{{ translate(options.description, true) }}</p>
            </div>
        </div>
        <div v-else-if="action == 'options'">
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::generic.placeholder') }}</label>
                    <language-input
                        class="voyager-input"
                        type="text" :placeholder="__('voyager::generic.placeholder')"
                        v-model="options.placeholder" />
                </div>
            </div>
            <div class="flex mb-4" v-if="options.range">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::generic.placeholder') }}</label>
                    <language-input
                        class="voyager-input"
                        type="text" :placeholder="__('voyager::generic.placeholder')"
                        v-model="options.placeholder_second" />
                </div>
            </div>
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::generic.type') }}</label>
                    <select class="voyager-input" v-model="options.type">
                        <option value="date">Date</option>
                        <option value="time">Time</option>
                        <option value="datetime">Date and Time</option>
                    </select>
                </div>
            </div>
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::generic.mode') }}</label>
                    <select class="voyager-input" v-model="options.mode">
                        <option value="future">Future</option>
                        <option value="past">Past</option>
                        <option value="all">All</option>
                    </select>
                </div>
            </div>
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::generic.format') }}</label>
                    <language-input
                        class="voyager-input"
                        type="text" :placeholder="__('voyager::generic.format')"
                        v-model="options.format" />
                </div>
            </div>
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::generic.range') }}</label>
                    <select v-model="options.range" class="voyager-input">
                        <option v-for="field in fields" v-bind:key="field">{{ field }}</option>
                    </select>
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
    props: ['value', 'options', 'fields', 'action'],
    data: function () {
        return {
            selectedDate: this.value,
            config: {
                wrap: true,
                format: this.options.format,
                enableTime: (this.options.type == 'datetime' || this.options.type == 'time'),
                enableSeconds: this.options.type == 'time',
                noCalendar: this.options.type == 'time',
                locale: this.$language.initial_locale,
                minDate: this.options.mode == 'future' ? new Date() : null,
                maxDate: this.options.mode == 'past' ? new Date() : null
            },
        };
    },
    mounted: function () {
        var vm = this;
        if (vm.options.range !== '') {
            vm.$globals.$on('formfield-input', function (field, value, translatable) {
                if (field == vm.options.range) {
                    Vue.set(vm.config, 'minDate', value);
                }
            });
        }
    }
};
</script>