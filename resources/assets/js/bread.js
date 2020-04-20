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

Vue.component('bread-manager-browse', require('../components/Manager/Browse').default);
Vue.component('bread-manager-edit-add', require('../components/Manager/EditAdd').default);
Vue.component('bread-manager-view', require('../components/Manager/View').default);
Vue.component('bread-manager-list', require('../components/Manager/List').default);
Vue.component('bread-manager-validation', require('../components/Manager/ValidationForm').default);