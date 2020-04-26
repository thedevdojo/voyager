var $store = {
    routes: [],
    formfields: [],
    breads: [],
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
    },
    toggleDirection () {
        this.rtl = !this.rtl;
        if (this.rtl) {
            document.querySelector('html').setAttribute('dir', 'rtl');
        } else {
            document.querySelector('html').setAttribute('dir', 'ltr');
        }
    },
    toggleDarkMode () {
        this.darkmode = !this.darkmode;
        if (this.darkmode) {
            document.querySelector('html').classList.add('mode-dark');
        } else {
            document.querySelector('html').classList.remove('mode-dark');
        }
    },
    toggleSidebar () {
        this.sidebarOpen = !this.sidebarOpen;
    },
    getFormfieldByType (type) {
        return this.formfields.filter(function (formfield) {
            return formfield.type == type;
        })[0];
    },
    getBreadByTable (table) {
        return this.breads.filter(function (bread) {
            return bread.table == table;
        })[0];
    }
};

Vue.prototype.$store = $store;
window.$store = $store;