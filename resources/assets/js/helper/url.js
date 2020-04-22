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

window.route = function () {
    var args = Array.prototype.slice.call(arguments);
    var name = args.shift();
    if (this.$store.routes[name] === undefined) {
        console.error('Route not found ', name);
    } else {
        return this.$store.routes[name]
            .split('/')
            .map(s => s[0] == '{' ? args.shift() : s)
            .join('/');
    }
};
Vue.prototype.route = route;