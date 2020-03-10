let formfields = [
    'Color',
    'DynamicDropdown',
    'HtmlElement',
    'DateTime',
    'Number',
    'Password',
    'Relationship',
    'Repeater',
    'RichTextEditor',
    'Slug',
    'Text',
];

formfields.forEach(function (formfield) {
    Vue.component('formfield-'+kebab_case(formfield), require('../components/Formfields/'+formfield).default);
});
Vue.component('formfield-mockup', require('../components/Manager/FormfieldMockup').default);