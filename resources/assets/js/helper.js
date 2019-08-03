Vue.prototype.kebab_case = function (input) {
    return input.replace(/([a-z])([A-Z])/g, '$1-$2').replace(/\s+/g, '-').toLowerCase();
}

Vue.prototype.get_input_as_translatable_object = function (input) {
    if (typeof input === 'string' || typeof input === 'number' || typeof input === 'boolean') {
        var value = input;
        input = {};
        input[this.$eventHub.locale] = value;
    }

    if (input && typeof input === 'object' && input.constructor === Object) {
        this.$eventHub.locales.forEach(function (locale) {
            if (!input.hasOwnProperty(locale)) {
                input[locale] = '';
            }
        });

        return input;
    }

    return {};
}

Vue.prototype.translate = function (input) {
    if (input && typeof input === 'object' && input.constructor === Object) {
        return input[this.$eventHub.locale] || '';
    }

    return input;
}

Vue.prototype.$eventHub = new Vue({
    data: {
        locale: document.getElementsByTagName('html')[0].getAttribute('lang'),
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

Vue.prototype.trans = function (key, replace = {})
{
    var translations = this.$eventHub.localization;
    let translation = key.split('.').reduce((t, i) => t[i] || null, translations);

    for (var placeholder in replace) {
        translation = translation.replace(`:${placeholder}`, replace[placeholder]);
    }

    return translation || key;
}

Vue.prototype.__ = function (key, replace = {})
{
    return this.trans(key, replace);
}

Vue.prototype.trans_choice = function (key, count = 1, replace = {})
{
    let translation = key.split('.').reduce((t, i) => t[i] || null, Vue.prototype.$eventHub.localization).split('|');

    translation = count > 1 ? translation[1] : translation[0];

    for (var placeholder in replace) {
        translation = translation.replace(`:${placeholder}`, replace[placeholder]);
    }

    return translation;
}

Vue.prototype.route = function ()
{
    var args = Array.prototype.slice.call(arguments);
    var name = args.shift();
    if (Vue.prototype.$eventHub.routes[name] === undefined) {
        console.error('Route not found ', name);
    } else {
        return Vue.prototype.$eventHub.routes[name]
            .split('/')
            .map(s => s[0] == '{' ? args.shift() : s)
            .join('/');
    }
}