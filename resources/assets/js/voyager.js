window.Vue = require('vue');

var app = new Vue({
    el: '#app',
    data: {
        page_loading: true
    },
    mounted: function () {
        var vm = this;

        document.addEventListener("DOMContentLoaded", function(event) {
            // Hide voyager-loader
            vm.page_loading = false;
        });
    }
});
