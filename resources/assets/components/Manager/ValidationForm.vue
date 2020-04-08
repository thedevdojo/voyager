<template>
    <div class="mt-5">
        <div class="w-full flex">
            <div class="w-4/6">
                <h2 class="text-lg">Validation</h2>
            </div>
            <div class="w-2/6 text-right">
                <button class="button green small" @click.stop="addRule">
                    <icon icon="plus" />
                </button>
            </div>
        </div>
        <div class="voyager-table">
            <table>
                <thead>
                    <tr>
                        <th>Rule</th>
                        <th>Message</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(rule, key) in reactiveRules" :key="'rule-'+key">
                        <td>
                            <input type="text" class="voyager-input w-full" v-model="rule.rule" placeholder="Rule">
                        </td>
                        <td>
                            <language-input
                                class="voyager-input w-full"
                                type="text" placeholder="Message"
                                v-bind:value="rule.message"
                                v-on:input="rule.message = $event" />
                        </td>
                        <td>
                            <button class="button red small" @click.stop="removeRule(key)">
                                <icon icon="trash" />
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    props: ['value'],
    data: function () {
        return {
            reactiveRules: this.value,
        };
    },
    methods: {
        addRule: function () {
            this.reactiveRules.push({
                rule: '',
                message: '',
            });
        },
        removeRule: function (key) {
            this.reactiveRules.splice(key, 1);
        }
    },
    watch: {
        reactiveRules: function (rules) {
            this.$emit('input', rules);
        }
    },
};
</script>