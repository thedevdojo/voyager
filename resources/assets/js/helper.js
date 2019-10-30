Vue.prototype.$globals = new Vue({
    data: {
        routes: [],
        formfields: [],
        breads: [],
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

window.getFormfieldByType = function (type) {
    var r_formfield = null;

    this.$globals.formfields.forEach(function (formfield) {
        if (formfield.type == type) {
            r_formfield = formfield;
        }
    });

    return r_formfield;
};
Vue.prototype.getFormfieldByType = getFormfieldByType;

window.getBreadByValue = function (value) {
    var r_bread = null;
    var vm = this;
    vm.$globals.breads.forEach(function (bread) {
        if (vm.translate(bread.name_singular, true) == value || vm.translate(bread.name_plural, true) == value || vm.translate(bread.slug, true) == value) {
            r_bread = bread;
        }
    });

    return r_bread;
}
Vue.prototype.getBreadByValue = getBreadByValue;

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

window.can = function ()
{
    
    return false;
};
Vue.prototype.can = can;

window.debug = function (message, printToConsole = true, type = 'log')
{
    if (printToConsole) {
        if (type == 'log')
            console.log(message);
        else if (type == 'error')
            console.error(message);
        else if (type == 'message')
            console.message(message);
    }

    message = message.replace(/'/g, "\\'");
    var clipmsg = '<br><a class="text-xs" href="#" onclick="return copyToClipboard(\''+message+'\')">'+this.__('voyager::generic.copy_to_clipboard')+'</a>';
    this.$snotify.html(message+clipmsg);
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