require('./vendor');
require('./helper');
require('./bread');
require('./formfields');

Vue.component('language-input', require('../components/LanguageInput').default);
Vue.component('locale-picker', require('../components/LocalePicker').default);