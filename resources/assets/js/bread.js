let bread_components = [
    'Bread/Browse',
    'Bread/EditAdd',
    'Bread/Read',
    'Bread/Actions',
];

bread_components.forEach(function (component) {
    var name = component.substring(component.lastIndexOf('/') + 1);
    Vue.component('bread-'+kebab_case(name), require('../components/'+component).default);
});

Vue.component('bread-builder-browse', require('../components/Builder/Browse').default);
Vue.component('bread-builder-edit-add', require('../components/Builder/EditAdd').default);
Vue.component('bread-builder-view', require('../components/Builder/View').default);
Vue.component('bread-builder-list', require('../components/Builder/List').default);
Vue.component('bread-builder-validation', require('../components/Builder/ValidationForm').default);