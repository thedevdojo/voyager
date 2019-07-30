let bread_components = [
    'Builder'
];

bread_components.forEach(function (component) {
    Vue.component('bread-'+Vue.prototype.kebab_case(component), require('../components/Bread/'+component).default);
});