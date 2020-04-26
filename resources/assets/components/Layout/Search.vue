<template>
    <div v-click-outside="close">
        <input
            autocomplete="off"
            type="text"
            class="py-2 hidden sm:block text-lg appearance-none bg-transparent leading-normal w-full search focus:outline-none"
            v-model="query" @input="search" :placeholder="placeholder">
        <input
            autocomplete="off"
            type="text"
            class="py-2 block sm:hidden text-lg appearance-none bg-transparent leading-normal w-full search focus:outline-none"
            v-model="query" @input="search" :placeholder="mobilePlaceholder">
        <dropdown ref="results_dd" pos="right">
            <div v-for="(bread, i) in searchResults" :key="'bread-results-'+i" class="p-4">
                <h4>{{ bread.bread }}</h4>
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
        </dropdown>
    </div>
</template>
<script>
export default {
    props: ['placeholder', 'mobilePlaceholder'],
    data: function () {
        return {
            searchResults: [],
            query: '',
        };
    },
    watch: {
        query: function (query) {
            this.search(query);
        }
    },
    methods: {
        close: function () {
            this.$refs.results_dd.close();
        },
        search: debounce(function (e) {
            var vm = this;
            vm.searchResults = [];
            if (vm.query == '') {
                return;
            }
            this.$store.breads.forEach(function (bread) {
                if (bread.global_search_field === null || bread.global_search_field == '') {
                    return;
                }
                axios.post(vm.route('voyager.search'), {
                    query: vm.query,
                    bread: bread.table,
                })
                .then(function (response) {
                    vm.searchResults.push(response.data[0]);
                    vm.$refs.results_dd.open();
                })
                .catch(function (errors) {
                    vm.$notify.error(error);
                    if (vm.$store.debug) {
                        vm.debug(error.response.data.message, true, 'error');
                    }
                });
            });
            
        }, 250),
        moreUrl: function (bread) {
            bread = this.$store.getBreadByTable(bread.table);

            return this.route('voyager.'+this.translate(bread.slug, true)+'.browse')+'?globalSearch='+this.query;
        },
        getResultUrl: function (bread, id) {
            bread = this.$store.getBreadByTable(bread.table);

            return this.route('voyager.'+this.translate(bread.slug, true)+'.read', id);
        }
    },
    mounted: function () {
        var vm = this;
        document.body.addEventListener('keydown', event => {
            if (event.keyCode === 27) {
                vm.search('');
            }
        });
    },
};
</script>
<style lang="scss" scoped>
.voyager-search-results {
    @apply absolute bg-white text-black rounded-lg border-gray-600 p-8 origin-top-left;
}

.mode-dark .voyager-search-results {
    @apply bg-black text-white;
}
</style>