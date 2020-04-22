Vue.prototype.$language = new Vue({
    data: {
        locale: document.getElementsByTagName('html')[0].getAttribute('lang'),
        initial_locale: document.getElementsByTagName('html')[0].getAttribute('lang'),
        locales: document.getElementsByTagName('html')[0].getAttribute('locales').split(','),
        localePicker: false,
        localization: [],
    },
    watch: {
        locale: function (locale) {
            // TODO: Set cookie?
        }
    },
});

Vue.mixin({
    methods: {
        get_input_as_translatable_object: function (input) {
            if (typeof input === 'string' || typeof input === 'number' || typeof input === 'boolean') {
                try {
                    input = JSON.parse(input);
                } catch (e) {
                    var value = input;
                    input = {};
                    input[this.$language.initial_locale] = value;
                }
            } else if (typeof input !== 'object' || !input) {
                input = {};
            }

            if (input && typeof input === 'object' && input.constructor === Object) {
                this.$language.locales.forEach(function (locale) {
                    if (!input.hasOwnProperty(locale)) {
                        Vue.set(input, locale, '');
                    }
                });
            }

            return input;
        },

        translate: function (input, once = false) {
            if (!this.isObject(input)) {
                input = this.get_input_as_translatable_object(input);
            }
            if (this.isObject(input)) {
                return input[once ? this.$language.initial_locale : this.$language.locale] || '';
            }

            return input;
        },

        trans: function (key, replace = {})
        {
            let translation = key.split('.').reduce((t, i) => t[i] || null, this.$language.localization);

            if (!translation) {
                if (this.debug) {
                    this.debug('Translation with key "'+key+'" does not exist.', true, 'warn');
                }

                return key;
            }

            for (var placeholder in replace) {
                translation = translation.replace(new RegExp(':'+placeholder, 'g'), replace[placeholder]);
            }

            return translation;
        },

        __: function (key, replace = {})
        {
            return this.trans(key, replace);
        },

        trans_choice: function (key, count = 1, replace = {})
        {
            let translation = key.split('.').reduce((t, i) => t[i] || null, this.$language.localization).split('|');

            translation = count > 1 ? translation[1] : translation[0];

            for (var placeholder in replace) {
                translation = translation.replace(`:${placeholder}`, replace[placeholder]);
            }

            return translation;
        },
    }
});