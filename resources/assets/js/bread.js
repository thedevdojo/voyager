let bread_components = [
    'Bread/Browse',
    'Bread/EditAdd',
    'Bread/Read',
    'Bread/Actions',

    'Manager/Builder',
    'Manager/BuilderBrowse',
    'Manager/ViewBuilder',
    'Manager/ListBuilder',
    'Manager/ValidationInput',
];

bread_components.forEach(function (component) {
    var name = component.substring(component.lastIndexOf('/') + 1);
    Vue.component('bread-'+kebab_case(name), require('../components/'+component).default);
});