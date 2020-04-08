const NotifyConfirm = {
    props: {
        content: {
            type: String
        },
        yes: {
            type: String
        },
        no: {
            type: String
        },
        yesClass: {
            type: String,
            default: 'green',
        },
        noClass: {
            type: String,
            default: 'red',
        }
    },
    template: 
    `<div class="text-white" @click.stop="">
        {{ content }}<br>
        <button @click="$emit('yes')" class="button mt-2" :class="yesClass">{{ yes }}</button>
        <button @click="$emit('no')" class="button mt-2" :class="noClass">{{ no }}</button>
    </div>`
};

const NotifyPrompt = {
    props: {
        content: {
            type: String
        },
        value: {
            type: String
        },
        ok: {
            type: String
        },
        cancel: {
            type: String
        },
        okClass: {
            type: String,
            default: 'green',
        },
        cancelClass: {
            type: String,
            default: 'red',
        },
    },
    data: function () {
        return {
            input: this.value
        }
    },
    mounted: function () {
        this.$refs.input.focus();
    },
    template: 
    `<div class="text-white" @click.stop="">
        {{ content }}
        <input type="text" class="voyager-input small w-full my-2" v-model="input" ref="input" v-on:keyup.enter="$emit('ok', input)" />
        <button @click="$emit('ok', input)" class="button" :class="okClass">{{ ok }}</button>
        <button @click="$emit('cancel')" class="button" :class="cancelClass">{{ cancel }}</button>
    </div>`
};

Vue.prototype.$notify = new Vue({
    methods: {
        default: function (content, options = false) {
            if (!options) {
                return this.$toast(content);
            }

            return this.$toast(content, options);
        },
        info: function (content, options = false) {
            if (!options) {
                return this.$toast.info(content);
            }

            return this.$toast.info(content, options);
        },
        success: function (content, options = false) {
            if (!options) {
                return this.$toast.success(content);
            }

            return this.$toast.success(content, options);
        },
        error: function (content, options = false) {
            if (!options) {
                return this.$toast.error(content);
            }

            return this.$toast.error(content, options);
        },
        warning: function (content, options = false) {
            if (!options) {
                return this.$toast.warning(content);
            }

            return this.$toast.warning(content, options);
        },
        confirm: function (content, yes, no, timeout = false, type = 'default') {
            var vm = this;
            return new Promise((resolve, reject) => {
                var id = null;
                var comp = {
                    component: NotifyConfirm,
                    props: {
                        content: content,
                        yes: yes,
                        no: no
                    },
                    listeners: {
                        yes: function () {
                            resolve(true);
                            vm.$toast.dismiss(id);
                        },
                        no: function () {
                            resolve(false);
                            vm.$toast.dismiss(id);
                        }
                    }
                };
                var options = {
                    timeout: timeout,
                    onClose: function () { reject(); }
                };
                id = vm.default(comp, options);

                if (timeout !== false) {
                    window.setTimeout(function () {
                        reject();
                    }, timeout);
                }
            });
        },
        prompt: async function (content, ok, cancel, timeout = false, value = '', type = 'default') {
            var vm = this;
            return new Promise((resolve, reject) => {
                var id = null;
                var comp = {
                    component: NotifyPrompt,
                    props: {
                        content: content,
                        ok: ok,
                        cancel: cancel,
                        value: value,
                    },
                    listeners: {
                        ok: function (input) {
                            resolve(input);
                            vm.$toast.dismiss(id);
                        },
                        cancel: function () {
                            reject();
                            vm.$toast.dismiss(id);
                        }
                    }
                };
                var options = {
                    timeout: timeout,
                    onClose: function () { reject(); }
                };
                id = vm.default(comp, options);
                if (timeout !== false) {
                    window.setTimeout(function () {
                        reject();
                    }, timeout);
                }
            });
        },
    }
});