window.Vue = require('vue');

window.debounce = require('debounce');
Vue.prototype.debounce = debounce;

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.head.querySelector('meta[name="csrf-token"]').content;

window.slugify = require('slugify');

import Transitions from 'vue2-transitions'
Vue.use(Transitions);

import draggable from 'vuedraggable';
Vue.component('draggable', draggable);

import { mixin as clickaway } from 'vue-clickaway';
Vue.mixin(clickaway);

import Toast from "vue-toastification";
Vue.use(Toast, {
    position: 'bottom-right',
});