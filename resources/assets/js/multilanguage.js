Vue.mixin({
    methods: {
        get_input_as_translatable_object: function (input) {
            if (typeof input === 'string' || typeof input === 'number' || typeof input === 'boolean') {
                try {
                    input = JSON.parse(input);
                } catch (e) {
                    var value = input;
                    input = {};
                    input[Vue.prototype.$eventHub.locale] = value;
                }
            }

            if (input && typeof input === 'object' && input.constructor === Object) {
                Vue.prototype.$eventHub.locales.forEach(function (locale) {
                    if (!input.hasOwnProperty(locale)) {
                        input[locale] = '';
                    }
                });

                return input;
            }

            return {};
        },

        translate: function (input, once = false) {
            if (!window.isObject(input)) {
                input = this.get_input_as_translatable_object(input);
            }
            if (window.isObject(input)) {
                return input[once ? Vue.prototype.$eventHub.initial_locale : Vue.prototype.$eventHub.locale] || '';
            }

            return input;
        },

        trans: function (key, replace = {})
        {
            var translations = Vue.prototype.$eventHub.localization;
            let translation = key.split('.').reduce((t, i) => t[i] || null, translations);

            for (var placeholder in replace) {
                translation = translation.replace(new RegExp(':'+placeholder, 'g'), replace[placeholder]);
            }

            return translation || key;
        },

        __: function (key, replace = {})
        {
            return this.trans(key, replace);
        },

        trans_choice: function (key, count = 1, replace = {})
        {
            let translation = key.split('.').reduce((t, i) => t[i] || null, Vue.prototype.$eventHub.localization).split('|');

            translation = count > 1 ? translation[1] : translation[0];

            for (var placeholder in replace) {
                translation = translation.replace(`:${placeholder}`, replace[placeholder]);
            }

            return translation;
        },
    }
});

Vue.prototype.$eventHub = new Vue({
    data: {
        locale: document.getElementsByTagName('html')[0].getAttribute('lang'),
        initial_locale: document.getElementsByTagName('html')[0].getAttribute('lang'),
        locales: document.getElementsByTagName('html')[0].getAttribute('locales').split(','),
        localization: [],
        routes: [],
        formfields: [],
    },
    methods: {
        getFormfieldByType: function (type) {
            var r_formfield = null;

            this.formfields.forEach(function (formfield) {
                if (formfield.type == type) {
                    r_formfield = formfield;
                }
            });

            return r_formfield;
        }
    }
});