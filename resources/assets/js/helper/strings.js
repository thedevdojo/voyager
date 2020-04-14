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

window.nl2br = function (input) {
    return input.replace(/\\n/g, '<br>');
};
Vue.prototype.nl2br = nl2br;

window.ucfirst = function (input) {
    return input[0].toUpperCase() + input.slice(1);
};
Vue.prototype.ucfirst = ucfirst;

window.uuid = function () {
    var dt = new Date().getTime();
    var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        var r = (dt + Math.random()*16)%16 | 0;
        dt = Math.floor(dt/16);
        return (c=='x' ? r :(r&0x3|0x8)).toString(16);
    });

    return uuid;
};
Vue.prototype.uuid = uuid;