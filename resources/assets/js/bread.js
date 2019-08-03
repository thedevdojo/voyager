let bread_components = [
    'Browse',
    'EditAdd',
    'Read',
    'Actions',
];

let bread_manager_components = [
    'EditAdd',
    'ViewBuilder',
    'ListBuilder'
];

bread_components.forEach(function (component) {
    Vue.component('bread-'+Vue.prototype.kebab_case(component), require('../components/Bread/'+component).default);
});

bread_manager_components.forEach(function (component) {
    Vue.component('bread-'+Vue.prototype.kebab_case(component), require('../components/Manager/'+component).default);
});