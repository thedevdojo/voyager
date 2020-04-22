require('./vendor');
require('./store');
require('./notify');
require('./helper');
require('./bread');
require('./multilanguage');
require('./formfields');
require('./layout');
require('./ui');

Vue.component('settings-manager', require('../components/Settings/Manager').default);
Vue.component('plugins-manager', require('../components/Plugins/Manager').default);
Vue.component('login', require('../components/Auth/Login').default);