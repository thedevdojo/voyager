window.Vue = require('vue');

window.debounce = require('debounce');
Vue.prototype.debounce = debounce;

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.head.querySelector('meta[name="csrf-token"]').content;

window.slugify = require('slugify');

import vSelect from 'vue-select';
Vue.component('v-select', vSelect);

import draggable from 'vuedraggable';
Vue.component('draggable', draggable);

var Snotify = require('vue-snotify');
Vue.use(Snotify, {
    toast: {
        timeout: 5000
    }
});

import Swatches from 'vue-swatches';
Vue.component('swatches', Swatches);

import VueDatePicker from '@mathieustan/vue-datepicker';
Vue.use(VueDatePicker);

import Popper from 'vue-popperjs';
Vue.component('popper', Popper);

import Transitions from 'vue2-transitions'
Vue.use(Transitions);

import { mixin as clickaway } from 'vue-clickaway';
Vue.mixin(clickaway);