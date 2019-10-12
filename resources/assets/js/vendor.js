window.Vue = require('vue');

window.debounce = require('debounce');

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.head.querySelector('meta[name="csrf-token"]').content;

import vSelect from 'vue-select';
Vue.component('v-select', vSelect);

import draggable from 'vuedraggable';
Vue.component('draggable', draggable);

var Snotify = require('vue-snotify');
Vue.use(Snotify);

import Popper from 'vue-popperjs';
Vue.component('popper', Popper);

import Swatches from 'vue-swatches';
Vue.component('swatches', Swatches);

import Multiselect from 'vue-multiselect';
Vue.component('multiselect', Multiselect);