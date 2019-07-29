Vue.prototype.kebab_case = function (input) {
    return input.replace(/([a-z])([A-Z])/g, '$1-$2').replace(/\s+/g, '-').toLowerCase();
}

Vue.prototype.get_input_as_translatable_object = function (input, locale) {
    
    return input;
    // If is object
    // If is array
    // Else make object with input as locale
}

Vue.prototype.$eventHub = new Vue({
    data: {
        locale: document.getElementsByTagName('html')[0].getAttribute('lang'),
        locales: document.getElementsByTagName('html')[0].getAttribute('locales').split(','),
    }
});