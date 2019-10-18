<template>
    <div>
        <div v-if="action == 'browse' || action == 'read'">
            <div v-if="options.range">
                {{ value.start || '' }} {{ translate(options.delimiter, true) }} {{ value.end || '' }}
            </div>
            <div v-else>
                {{ value }}
            </div>
        </div>
        <div v-else-if="action == 'edit' || action == 'add' || action == 'mockup'" class="flex mb-4">
            <div class="w-full m-1">
                <label class="voyager-label">{{ translate(options.title, true) }}</label>
                <div class="flex">
                    <VueDatePicker
                        v-bind:value="value"
                        v-on:input="$emit('input', $event)"
                        no-header
                        :locale="{ lang: $globals.initial_locale }"
                        :range="options.range || false"
                        :min-date="options.mode == 'future' ? new Date() : ''"
                        :max-date="options.mode == 'past' ? new Date() : ''"
                        :placeholder="translate(options.placeholder, true)"
                        :type="dateType"
                        :disabled="false" />
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
            <div class="flex mb-4">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::generic.type') }}</label>
                    <select class="voyager-input" v-model="options.type">
                        <option value="date">Date</option>
                        <option value="time">Time</option>
                        <option value="datetime">Date and Time</option>
                        <option value="month">Month</option>
                    </select>
                </div>
            </div>
            <div class="flex mb-4" v-if="type == 'view'">
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
                    <label class="voyager-label text-gray-100">{{ __('voyager::generic.range') }}</label>
                    <input type="checkbox" v-model="options.range">
                </div>
            </div>
            <div class="flex mb-4" v-if="options.range">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::generic.field') }}</label>
                    <select v-model="options.field_second" class="voyager-input">
                        <option v-for="field in fields" v-bind:key="field">{{ field }}</option>
                    </select>
                </div>
            </div>
            <div class="flex mb-4" v-if="options.range">
                <div class="w-full m-1">
                    <label class="voyager-label text-gray-100">{{ __('voyager::generic.delimiter') }}</label>
                    <language-input
                        class="voyager-input"
                        type="text" :placeholder="__('voyager::generic.delimiter')"
                        v-model="options.delimiter" />
                </div>
            </div>
        </div>
        <div v-else-if="action == 'query'">
            <VueDatePicker
                no-header
                :locale="{ lang: $globals.initial_locale }"
                :range="true"
                v-model="filterDate"
                @onChange="filter()"
                :placeholder="placeholder" />
        </div>
    </div>
</template>

<script>
export default {
    props: ['value', 'options', 'fields', 'action', 'placeholder', 'type'],
    data: function () {
        return {
            filterDate: new Date(),
        };
    },
    computed: {
        dateType: function () {
            var type = this.options.type;
            if (type == 'month' || type == 'quarter') {
                return type;
            }

            return 'date';
        }
    },
    methods: {
        filter: function () {
            this.$emit('input', this.filterDate);
        }
    },
    watch: {
        'value': function (value) {
            if (!value) {
                // TODO: Close datepicker
            }
            this.filterDate = value;
        }
    }
};
</script>