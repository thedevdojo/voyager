Vue.prototype.$globals = new Vue({
    data: {
        routes: [],
        formfields: [],
        breads: [],
        csrf_token: document.head.querySelector('meta[name="csrf-token"]').content,
        permissions: [],
        debug: false,
        darkmode: false,
    },
    methods: {
        toggleDarkMode: function () {
            var dark = true;
            var classes = document.querySelector('html').classList;
            if (classes.contains('mode-dark')) {
                classes.remove('mode-dark');
                dark = false;
            } else {
                classes.add('mode-dark');
            }

            this.setCookie('dark-mode', (dark ? 'true' : 'false'), 360);
            this.darkmode = dark;
        },
        setCookie: function (name, value, exdays) {
            var d = new Date();
            d.setTime(d.getTime() + (exdays*24*60*60*1000));
            var expires = "expires="+ d.toUTCString();
            document.cookie = name + "=" + value + ";" + expires + ";path=/";
        },
        getCookie: function (name) {
            var name = name + "=";
            var decodedCookie = decodeURIComponent(document.cookie);
            var ca = decodedCookie.split(';');
            for(var i = 0; i <ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        }
    },
    created: function () {
        var dark_mode = this.getCookie('dark-mode');
        if (dark_mode == 'true') {
            this.toggleDarkMode();
        }
    }
});

window.kebab_case = function (input, char = '-') {
    return input.replace(/([a-z])([A-Z])/g, '$1-$2').replace(/\s+/g, char).toLowerCase();
};
Vue.prototype.kebab_case = kebab_case;

window.snake_case = function (input) {
    return window.kebab_case(input, '_');
};
Vue.prototype.snake_case = snake_case;

window.title_case = function (input) {
    input = this.snake_case(input).split('_');
    for (var i = 0; i < input.length; i++) {
        input[i] = input[i].charAt(0).toUpperCase() + input[i].slice(1); 
    }

    return input.join(' ');
};
Vue.prototype.title_case = title_case;

window.studly = function (input) {
    input = this.kebab_case(input).split('-');
    for (var i = 0; i < input.length; i++) {
        input[i] = input[i].charAt(0).toUpperCase() + input[i].slice(1); 
    }

    return input.join('');
};
Vue.prototype.studly = studly;

window.slug = function (input) {
    return window.slugify(input);
};
Vue.prototype.slug = slug;

window.route = function ()
{
    var args = Array.prototype.slice.call(arguments);
    var name = args.shift();
    if (!this.$globals) {
        return;
    }
    if (this.$globals.routes[name] === undefined) {
        console.error('Route not found ', name);
    } else {
        return this.$globals.routes[name]
            .split('/')
            .map(s => s[0] == '{' ? args.shift() : s)
            .join('/');
    }
};
Vue.prototype.route = route;

window.debug = function (message, printToConsole = true, type = 'log')
{
    if (printToConsole) {
        if (type == 'log')
            console.log(message);
        else if (type == 'error')
            console.error(message);
        else if (type == 'message')
            console.message(message);
        else if (type == 'warn')
            console.warn(message);
    }
}
Vue.prototype.debug = debug;

window.copyToClipboard = function (message)
{
    var dummy = document.createElement('textarea');
    document.body.appendChild(dummy);
    dummy.value = message.replace(/\'/g, "'");
    dummy.select();
    document.execCommand('copy');
    document.body.removeChild(dummy);

    return false;
}
Vue.prototype.copyToClipboard = copyToClipboard;

window.isArray = function (input)
{
    return (input && typeof input === 'object' && input instanceof Array);
};
Vue.prototype.isArray = isArray;

window.isObject = function (input)
{
    return (input && typeof input === 'object' && input.constructor === Object);
};
Vue.prototype.isObject = isObject;

window.clamp = function (num, min, max) {
    return num <= min ? min : num >= max ? max : num;
};
Vue.prototype.clamp = clamp;

window.addParameterToUrl = function (parameter, value, url = null) {
    var newurl = new URL(document.location.href);

    if (url)  {
        newurl = new URL(url);
    }
    newurl.searchParams.set(parameter, value);

    return newurl;
};
Vue.prototype.addParameterToUrl = addParameterToUrl;

window.getParameterFromUrl = function (key, default_value = null, url = null) {
    var newurl = new URL(document.location.href);

    if (url)  {
        newurl = new URL(url);
    }

    return newurl.searchParams.get(key) || default_value;
};
Vue.prototype.getParameterFromUrl = getParameterFromUrl;

window.getParametersFromUrl = function (url = null) {
    var newurl = new URL(document.location.href);

    if (url)  {
        newurl = new URL(url);
    }

    return newurl.searchParams.entries();
};
Vue.prototype.getParametersFromUrl = getParametersFromUrl;

window.pushToUrlHistory = function (url) {
    window.history.pushState({ path:  url.href }, '', url.search);
};
Vue.prototype.pushToUrlHistory = pushToUrlHistory;

window.uuid = function () {
    return ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c =>
        (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
    );
};
Vue.prototype.uuid = uuid;

window.nl2br = function (input) {
    return input.replace(/\\n/g, '<br>');
};
Vue.prototype.nl2br = nl2br;

Vue.directive('click-outside', {
    bind: function (el, binding, vnode) {
        el.clickOutsideEvent = function (event) {
            if (!(el == event.target || el.contains(event.target))) {
                vnode.context[binding.expression](event);
            }
        };
        document.body.addEventListener('click', el.clickOutsideEvent)
    },
    unbind: function (el) {
        document.body.removeEventListener('click', el.clickOutsideEvent)
    },
});