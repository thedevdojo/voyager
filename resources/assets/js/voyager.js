require('./vendor');
require('./helper');
require('./bread');
require('./multilanguage');
require('./formfields');
require('./ui');

Vue.component('language-input', require('../components/LanguageInput').default);
Vue.component('locale-picker', require('../components/LocalePicker').default);
Vue.component('user-dropdown', require('../components/UserDropdown').default);
Vue.component('settings-manager', require('../components/Settings/Manager').default);
Vue.component('plugins-manager', require('../components/Plugins/Manager').default);
Vue.component('login', require('../components/Auth/Login').default);