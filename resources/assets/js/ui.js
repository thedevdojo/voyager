/**********************************************************************
 *  
 *  Global UI Functionality for Voyager
 * 
 *  This is custom js functionality for the Voyager UI may include
 *  functionality for dropdowns, modals, textbox, etc...
 * 
 **********************************************************************/


let components = [
    'Alert',
    'Badge',
    'Card',
    'Collapsible',
    'ColorPicker',
    'Dropdown',
    'IconPicker',
    'LanguageInput',
    'Modal',
    'Notifications',
    'Pagination',
    'SlideIn',
    'SortContainer',
    'SortElement',
    'Tabs',
];

components.forEach(function (component) {
    Vue.component(kebab_case(component), require('../components/UI/'+component).default);
});