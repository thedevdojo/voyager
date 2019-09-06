window.kebab_case = function (input, char = '-') {
    return input.replace(/([a-z])([A-Z])/g, '$1-$2').replace(/\s+/g, char).toLowerCase();
};
window.snake_case = function (input) {
    return window.kebab_case(input, '_');
};
window.route = function ()
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
};
window.isArray = function (input)
{
    return (input && typeof input === 'object' && input instanceof Array);
};
window.isObject = function (input)
{
    return (input && typeof input === 'object' && input.constructor === Object);
};
window.clamp = function (num, min, max) {
    return num <= min ? min : num >= max ? max : num;
};