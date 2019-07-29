require('./vendor');
require('./helper');
require('./bread');
require('./formfields');

var voyager = new Vue({
    el: '#voyager',
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
