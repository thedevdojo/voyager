import Vuex from 'vuex';
Vue.use(Vuex);

Vue.prototype.store = new Vuex.Store({
    state: {
        routes: [],
        csrf_token: document.head.querySelector('meta[name="csrf-token"]').content,
        debug: false,
        darkmode: false,
        rtl: false,
        sidebarOpen: true,
        pageLoading: true,
        ui: {
            name: 'Voyager',
            colors: [
                'red',
                'orange',
                'yellow',
                'green',
                'teal',
                'blue',
                'indigo',
                'purple',
                'pink',
            ],
            lorem: 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Aliquid pariatur, ipsum similique veniam quo totam eius aperiam dolorum.',
        }
    },
    mutations: {
        routes (state, routes) {
            state.routes = routes;
        },
        formfields (state, formfields) {
            state.formfields = formfields;
        },
        breads (state, breads) {
            state.breads = breads;
        },
        debug (state, debug) {
            state.debug = debug;
        },
        pageLoading (state, value) {
            state.pageLoading = value;
        },
        toggleDarkMode (state) {
            state.darkmode = !state.darkmode;
            if (state.darkmode) {
                document.querySelector('html').classList.add('mode-dark');
            } else {
                document.querySelector('html').classList.remove('mode-dark');
            }
        },
        toggleDirection (state) {
            state.rtl = !state.rtl;
            if (state.rtl) {
                document.querySelector('html').setAttribute('dir', 'rtl');
            } else {
                document.querySelector('html').setAttribute('dir', 'ltr');
            }
        },
        toggleSidebar (state) {
            state.sidebarOpen = !state.sidebarOpen;
        },
        setSidebar (state, value) {
            state.sidebarOpen = value;
        },
    }
});