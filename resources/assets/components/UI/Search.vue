<template>
    <div>
        <input type="text" class="py-2 block text-lg appearance-none bg-transparent leading-normal w-full search focus:outline-none" @input="search" placeholder="search for users, posts, etc...">
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

            return this.route('voyager.'+this.translate(bread.slug, true)+'.browse')+'?globalSearch='+this.query;
        },
        getResultUrl: function (bread, id) {
            bread = this.getBreadByValue(bread.bread);

            return this.route('voyager.'+this.translate(bread.slug, true)+'.read', id);
        }
    },
};
</script>