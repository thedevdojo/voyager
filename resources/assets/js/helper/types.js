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

window.isString = function (input)
{
    return (typeof input === 'string');
};
Vue.prototype.isString = isString;