require('./vendor');
require('./helper');
require('./bread');
require('./multilanguage');
require('./formfields');
require('./ui');

Vue.component('language-input', require('../components/LanguageInput').default);
Vue.component('locale-picker', require('../components/LocalePicker').default);
Vue.component('user-dropdown', require('../components/UserDropdown').default);