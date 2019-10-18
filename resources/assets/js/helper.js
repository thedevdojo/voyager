Vue.prototype.$globals = new Vue({
    data: {
        routes: [],
        formfields: [],
        breads: [],
        permissions: [],
        debug: false,
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