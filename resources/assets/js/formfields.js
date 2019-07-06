let formfields = [
    
];

import BaseFormfield from '../components/Formfields/BaseFormfield';
Vue.component('formfield-base', BaseFormfield);

formfields.forEach(function (formfield) {
    Vue.component('formfield-'+Vue.prototype.kebab_case(formfield), require('../components/Formfields/'+formfield).default);
});
