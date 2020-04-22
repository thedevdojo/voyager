let components = [
    'Icon',
    'LocalePicker',
    'MenuItem',
    'Search',
    'UserDropdown',
];

components.forEach(function (component) {
    Vue.component(kebab_case(component), require('../components/Layout/'+component).default);
});