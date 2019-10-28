<template>
    <div>
        <div class="voyager-card">
            <div class="body">
                <select class="voyager-input mb-3 w-auto" v-model="currentGroup">
                    <option :value="null">{{ __('voyager::generic.no_group') }}</option>
                    <option v-for="group in groups" :key="group" :value="group">{{ title_case(group) }}</option>
                </select>
                <div class="flex flex-wrap">
                    <div v-for="(setting, i) in groupedSettings" v-bind:key="'setting-'+i" class="w-full voyager-card bordered">
                        <div class="body">
                            <language-input
                                class="voyager-input small w-1/5"
                                type="text" :placeholder="__('voyager::generic.title')"
                                v-bind:value="setting.title"
                                v-on:input="setting.title = $event" />
                            <input
                                type="text"
                                class="voyager-input small w-1/5"
                                :placeholder="__('voyager::generic.key')"
                                v-bind:value="setting.key"
                                v-on:input="setKey($event, setting)" />
                            <input
                                type="text"
                                class="voyager-input small w-1/5"
                                :placeholder="__('voyager::generic.group')"
                                v-on:input="setGroup($event, setting)"
                                v-bind:value="setting.group" />
                            <button class="button red small" @click="deleteSetting(setting)">{{ __('voyager::generic.delete') }}</button>
                            <component
                                :is="'formfield-'+setting.type"
                                v-bind:value="data(i, null)"
                                v-on:input="data(i, $event)"
                                :options="setting.options"
                                column=""
                                action="edit" />
                        </div>
                    </div>
                </div>

                <select @change="addSetting" class="voyager-input w-full">
                    <option v-bind:value="''">{{ __('voyager::generic.add_setting') }}</option>
                    <option v-for="(formfield, i) in formfields" v-bind:value="formfield.type" v-bind:key="i">
                        {{ formfield.name }}
                    </option>
                </select>
                <button class="button blue mt-3" @click="saveSettings">{{ __('voyager::generic.save') }}</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['input', 'url'],
    data: function () {
        return {
            settings: this.input,
            currentGroup: null,
            charWarningTriggered: false,
        };
    },
    computed: {
        formfields: function () {
            var vm = this;
            return this.$globals.formfields.filter(function (formfield) {
                return formfield.settings;
            });
        },
        groups: function () {
            var groups = [];
            this.settings.forEach(function (setting) {
                if (setting.group && setting.group !== '' && !groups.includes(setting.group)) {
                    groups.push(setting.group);
                }
            });

            return groups;
        },
        groupedSettings: function () {
            var vm = this;
            return this.settings.filter(function (setting) {
                return setting.group == vm.currentGroup;
            });
        }
    },
    methods: {
        addSetting: function (event) {
            var formfield = this.$globals.formfields.filter(function (formfield) {
                return formfield.type == event.target.value;
            }).pop();

            if (formfield) {
                var options = JSON.parse(JSON.stringify(formfield.options));
                options.link = false;
                this.settings.push({
                    type: formfield.type,
                    options: options,
                    value: this.get_input_as_translatable_object(''),
                    title: '',
                    group: this.currentGroup,
                    key: '',
                    rules: []
                });
            }

            event.target.value = '';
        },
        deleteSetting: function (setting) {
            var vm = this;
            vm.$snotify.confirm(vm.__('voyager::generic.delete_setting_confirm'), vm.__('voyager::generic.delete_setting'), {
                timeout: 5000,
                showProgressBar: true,
                closeOnClick: false,
                pauseOnHover: true,
                buttons: [
                    {
                        text: vm.__('voyager::generic.yes'),
                        action: (toast) => {
                            vm.settings = vm.settings.filter(function (set) {
                                return set !== setting;
                            });
                            if (vm.groupedSettings.length == 0) {
                                vm.currentGroup = null;
                            }
                            vm.$snotify.remove(toast.id);
                        }
                    },
                    {
                        text: vm.__('voyager::generic.no'),
                        action: (toast) => {
                            vm.$snotify.remove(toast.id);
                        }
                    },
                ]
            });
        },
        saveSettings: function () {
            var vm = this;
            axios.post(this.url, {
                settings: vm.settings,
                _token: document.head.querySelector('meta[name="csrf-token"]').content,
            })
            .then(function (response) {
                vm.$snotify.success(vm.__('voyager::generic.settings_saved'));
            })
            .catch(function (errors) {
                var errors = errors.response.data;
                Object.entries(errors).forEach(([key, val]) => {
                    val.forEach(function (e) {
                        vm.$snotify.error(e);
                    });
                });
            });
        },
        data: function (num, value = null) {
            if (value) {
                Vue.set(this.groupedSettings[num].value, this.$language.locale, value);
            }

            return this.translate(this.groupedSettings[num].value || '');
        },
        setKey: function (event, setting) {
            var new_value = this.checkInput(event.target.value);
            setting.key = this.emptyToNull(new_value);
        },
        setGroup: function (event, setting) {
            var new_value = this.checkInput(event.target.value);
            setting.group = this.emptyToNull(new_value);
            
            Vue.set(this, 'currentGroup', setting.group);
        },
        checkInput: function (value) {
            if (!value.match(/^[a-z_]+$/)) {
                if (!this.charWarningTriggered) {
                    this.charWarningTriggered = true;
                    this.$snotify.warning(this.__('voyager::generic.settings_char_warning'));
                }

                return value.replace(/[^a-z_]/g, '');
            }

            return value;
        },
        emptyToNull: function (value) {
            if (value == '') {
                return null;
            }

            return value;
        }
    },
    mounted: function () {
        var vm = this;
        Vue.prototype.$language.localePicker = true;
        if (!vm.isArray(vm.settings)) {
            var data = [];
            try {
                data = JSON.parse(vm.settings);
            } catch (e) {
                
            }
            vm.settings = data;
        }

        vm.settings.forEach(function (setting) {
            Vue.set(vm.settings, 'value', vm.get_input_as_translatable_object(setting.value));
        });
    }
};
</script>