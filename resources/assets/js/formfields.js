let formfields = [
    'FormfieldMockup',
    'Text'
];

formfields.forEach(function (formfield) {
    if (formfield.startsWith('Formfield')) {
        Vue.component(Vue.prototype.kebab_case(formfield), require('../components/Formfields/'+formfield).default);
    } else {
        Vue.component('formfield-'+Vue.prototype.kebab_case(formfield), require('../components/Formfields/'+formfield).default);
    }
});
