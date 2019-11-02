/**********************************************************************
 *  
 *  Global UI Functionality for Voyager
 * 
 *  This is custom js functionality for the Voyager UI may include
 *  functionality for dropdowns, modals, textbox, etc...
 * 
 **********************************************************************/

 /*
 * VOYAGER TEXT BOX
 *
 * This functionality is for a textbox container using the .voyager-input-container class
 */

Vue.component('v-input', require('../components/UI/V-input').default);
Vue.component('v-button', require('../components/UI/V-button').default);
Vue.component('pagination', require('../components/UI/Pagination').default);
Vue.component('floating-button', require('../components/UI/FloatingButton').default);
Vue.component('tooltip', require('../components/UI/Tooltip').default);
Vue.component('search', require('../components/UI/Search').default);
Vue.component('slidein', require('../components/UI/SlideIn').default);
Vue.component('icon', require('../components/UI/Icon').default);

