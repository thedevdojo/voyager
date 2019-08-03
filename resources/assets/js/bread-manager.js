let bread_manager_components = [
    'EditAdd',
    'ViewBuilder',
    'ListBuilder'
];

bread_manager_components.forEach(function (component) {
    Vue.component('bread-'+Vue.prototype.kebab_case(component), require('../components/Manager/'+component).default);
});