import './helper/cookies';
import './helper/math';
import './helper/misc';
import './helper/strings';
import './helper/types';
import './helper/url';

Vue.directive('click-outside', {
    bind: function (el, binding, vnode) {
        el.clickOutsideEvent = function (event) {
            if (!(el == event.target || el.contains(event.target))) {
                vnode.context[binding.expression](event);
            }
        };
        document.body.addEventListener('click', el.clickOutsideEvent);
    },
    unbind: function (el) {
        document.body.removeEventListener('click', el.clickOutsideEvent);
    },
});

Vue.directive('focus', {
    inserted: function (el) {
        el.focus();
    }
});

Vue.prototype.$eventHub = new Vue({});