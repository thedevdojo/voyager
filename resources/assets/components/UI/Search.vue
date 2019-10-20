<template>
    <div>
        <div class="absolute mt-3">
            <svg width="25" height="25" fill="currentColor" class="text-gray-500" xmlns="http://www.w3.org/2000/svg"><path d="M19.41 19.41c-4.24 4.239-11.112 4.239-15.352 0-4.24-4.24-4.24-11.112 0-15.352s11.112-4.24 15.352 0c4.239 4.24 4.239 11.112 0 15.352zm-1.706-1.706a8.441 8.441 0 000-11.939 8.441 8.441 0 00-11.939 0 8.441 8.441 0 000 11.94 8.441 8.441 0 0011.94 0zm2.558 4.265l1.706-1.706 2.55 2.551a1.21 1.21 0 01.009 1.714 1.204 1.204 0 01-1.714-.008l-2.55-2.551z" /></svg>            
        </div>
        <input type="text" class="voyager-search" @input="search" placeholder="Search">
        <div v-if="searchResults.length > 0" class="voyager-search-results">
            <div v-for="(bread, i) in searchResults" :key="'bread-results-'+i">
                {{ bread.bread }}
                <div v-if="bread.results.length == 0">
                    {{ __('voyager::generic.no_results') }}
                </div>
                <div v-else>
                    <a v-for="(result, c) in bread.results.slice(0, 3)" :key="'bread-result-'+c" :href="getResultUrl(bread, result.id)">
                        {{ result.data }}<br>
                    </a>
                    <div v-if="bread.results.length > 3">
                        <a :href="moreUrl(bread)">
                            {{ __('voyager::generic.more_results', {num: (bread.results.length - 3)}) }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data: function () {
        return {
            searchResults: [],
            query: '',
            loading: false
        };
    },
    methods: {
        search: debounce(function (e) {
            var vm = this;
            vm.loading = true;
            vm.query = e.target.value;
            vm.searchResults = [];
            this.$globals.breads.forEach(function (bread) {
                axios.post(vm.route('voyager.search'), {
                    query: vm.query,
                    bread: bread.table,
                })
                .then(function (response) {
                    vm.searchResults = response.data;
                    vm.loading = false;
                })
                .catch(function (errors) {
                    vm.$snotify.error(error);
                    if (vm.debug) {
                        vm.debug(error.response.data.message, true, 'error');
                    }
                    vm.loading = false;
                });
            });
            
        }, 250),
        moreUrl: function (bread) {
            bread = this.getBreadByValue(bread.bread);

            return this.route('voyager.'+this.translate(bread.slug, true)+'.index')+'?globalSearch='+this.query;
        },
        getResultUrl: function (bread, id) {
            bread = this.getBreadByValue(bread.bread);

            return this.route('voyager.'+this.translate(bread.slug, true)+'.show', id);
        }
    },
};
</script>