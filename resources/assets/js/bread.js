let bread_components = [
    'Builder'
];

bread_components.forEach(function (component) {
    Vue.component('bread-'+Vue.prototype.kebab_case(component), require('../components/Bread/'+component).default);
});

Vue.component('language-input', require('../components/Misc/LanguageInput').default);
Vue.component('locale-picker', require('../components/Misc/LocalePicker').default);