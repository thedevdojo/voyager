<template>
    <div v-click-outside="close">
        <input autocomplete="off" type="text" class="py-2 block text-lg appearance-none bg-transparent leading-normal w-full search focus:outline-none" v-model="query" @input="search" placeholder="Search for users, posts, etc...">
        <div v-if="searchResults.length > 0 && opened" class="voyager-search-results">
            <div v-for="(bread, i) in searchResults" :key="'bread-results-'+i">
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
        </div>
    </div>
</template>
<script>
export default {
    data: function () {
        return {
            searchResults: [],
            query: '',
            opened: false,
        };
    },
    watch: {
        query: function (query) {
            this.search(query);
        }
    },
    methods: {
        close: function () {
            this.opened = false;
        },
        search: debounce(function (e) {
            var vm = this;
            vm.searchResults = [];
            if (vm.query == '') {
                return;
            }
            this.$globals.breads.forEach(function (bread) {
                if (bread.global_search_field === null || bread.global_search_field == '') {
                    return;
                }
                axios.post(vm.route('voyager.search'), {
                    query: vm.query,
                    bread: bread.table,
                })
                .then(function (response) {
                    vm.searchResults.push(response.data[0]);
                    vm.opened = true;
                })
                .catch(function (errors) {
                    vm.$snotify.error(error);
                    if (vm.debug) {
                        vm.debug(error.response.data.message, true, 'error');
                    }
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