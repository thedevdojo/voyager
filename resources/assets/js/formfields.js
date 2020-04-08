let formfields = [
    'Repeater',
    'Text',
];

formfields.forEach(function (formfield) {
    var name = kebab_case(formfield);
    Vue.component('formfield-'+name+'-browse', require('../components/Formfields/'+formfield+'/Browse').default);
    Vue.component('formfield-'+name+'-read', require('../components/Formfields/'+formfield+'/Read').default);
    Vue.component('formfield-'+name+'-edit-add', require('../components/Formfields/'+formfield+'/EditAdd').default);
    Vue.component('formfield-'+name+'-builder', require('../components/Formfields/'+formfield+'/Builder').default);
});