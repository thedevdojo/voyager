<template>
    <table class="w-full text-white">
        <thead>
            <tr>
                <th>{{ __('voyager::manager.rule') }}</th>
                <th>{{ __('voyager::manager.message') }}</th>
                <th>{{ __('voyager::generic.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(rule, i) in rules" v-bind:key="'rule-'+i">
                <th>
                    <input v-model="rule.rule" class="voyager-input" :placeholder="__('voyager::manager.rule')">
                </th>
                <th>
                    <language-input
                        class="voyager-input"
                        type="text" :placeholder="__('voyager::manager.message')"
                        v-bind:value="rule.message"
                        v-on:input="rule.message = $event" />
                </th>
                <th>
                    <button class="button red" @click="removeRule(i)">{{ __('voyager::generic.delete') }}</button>
                </th>
            </tr>
        </tbody>
        <tfoot>
            <button class="button green" @click="addRule">{{ __('voyager::generic.add') }}</button>
        </tfoot>
    </table>
</template>

<script>
export default {
    props: ['rules'],
    methods: {
        addRule: function () {
            this.rules.push({
                rule: '',
                message: ''
            });
        },
        removeRule: function (id) {
            this.rules.splice(id, 1);
        },
    }
};
</script>