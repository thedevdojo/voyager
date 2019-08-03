let bread_components = [
    'Bread/Browse',
    'Bread/EditAdd',
    'Bread/Read',
    'Bread/Actions',

    'Manager/Builder',
    'Manager/ViewBuilder',
    'Manager/ListBuilder'
];

bread_components.forEach(function (component) {
    var name = component.substring(component.lastIndexOf('/') + 1);
    Vue.component('bread-'+Vue.prototype.kebab_case(name), require('../components/'+component).default);
});