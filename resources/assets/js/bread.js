let bread_components = [
    'Browse',
    'EditAdd',
    'Read',
    'Actions',
];

bread_components.forEach(function (component) {
    Vue.component('bread-'+Vue.prototype.kebab_case(component), require('../components/Bread/'+component).default);
});