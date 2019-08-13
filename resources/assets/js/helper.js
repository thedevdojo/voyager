Vue.prototype.kebab_case = function (input) {
    return input.replace(/([a-z])([A-Z])/g, '$1-$2').replace(/\s+/g, '-').toLowerCase();
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

Vue.prototype.isArray = function (input)
{
    return (input && typeof input === 'object' && input instanceof Array);
}

Vue.prototype.isObject = function (input)
{
    return (input && typeof input === 'object' && input.constructor === Object);
}

Vue.prototype.clamp = function (num, min, max) {
    return num <= min ? min : num >= max ? max : num;
}