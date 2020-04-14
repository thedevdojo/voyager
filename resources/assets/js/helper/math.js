window.clamp = function (num, min, max) {
    return num <= min ? min : num >= max ? max : num;
};
Vue.prototype.clamp = clamp;