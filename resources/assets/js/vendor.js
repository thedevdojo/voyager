// https://github.com/vuejs/vue
window.Vue = require('vue');

// https://github.com/component/debounce
window.debounce = require('debounce');
Vue.prototype.debounce = debounce;

// https://github.com/axios/axios
window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.head.querySelector('meta[name="csrf-token"]').content;

// https://github.com/simov/slugify
window.slugify = require('slugify');
Vue.prototype.slugify = window.slugify;

// https://github.com/BinarCode/vue2-transitions
import Transitions from 'vue2-transitions';
Vue.use(Transitions);

// https://github.com/Jexordexan/vue-slicksort
import { HandleDirective } from 'vue-slicksort';
Vue.directive('sort-handle', HandleDirective);

// https://github.com/rigor789/vue-scrollto
import VueScrollTo from 'vue-scrollto';
Vue.use(VueScrollTo, {
    container: 'main',
});