<template>
    <div class="button-group">
        <button class="button blue" @click="openPreviousPage" :disabled="previousButtonDisabled">&lt;</button>
        <button
            v-for="(page, i) in showingPages" :key="i"
            class="button blue"
            :class="[page == currentPage ? 'active' : '']"
            :disabled="page == '...'"
            @click="openPage(page)">
            {{ page }}
        </button>
        <button class="button blue" @click="openNextPage" :disabled="nextButtonDisabled">&gt;</button>
    </div>
</template>

<script>
export default {
    props: ['pages', 'value', 'visiblePages'],
    data: function () {
        return {
            currentPage: parseInt(this.value)
        };
    },
    computed: {
        previousButtonDisabled: function () {
            return this.value === 1;
        },
        nextButtonDisabled: function () {
            return this.value == this.pages;
        },
        showingPages: function () {
            var vm = this;
            var visiblePages = vm.visiblePages;
            if (visiblePages > vm.pages) {
                visiblePages = vm.pages;
            }
            var threshold = (visiblePages - 1) / 2;
            if (vm.pages == 1) {
                return 1;
            }
            var pagesArray = Array(visiblePages - 1).fill(0);            

            if (vm.value <= threshold + 1) {
                pagesArray[0] = 1;
                const finalPages = pagesArray.map((paginationTrigger, index) => {
                    return pagesArray[0] + index;
                });
                finalPages.push('...');
                finalPages.push(vm.pages);

                return finalPages;
            }

            if (vm.value >= vm.pages - threshold + 1) {
                const finalPages = pagesArray.map((paginationTrigger, index) => {
                    return vm.pages - index;
                });
                
                finalPages.push('...');
                finalPages.reverse().unshift(1);

                return finalPages;
            }

            pagesArray[0] = vm.value - threshold + 1;
            const finalPages = pagesArray.map((paginationTrigger, index) => {
                return pagesArray[0] + index;
            });
            finalPages.push('...');
            finalPages.push('');
            finalPages.unshift('...');
            finalPages.unshift(1);
            finalPages[finalPages.length - 1] = vm.pages;

            return finalPages;
        }
    },
    methods: {
        openPage: function (page) {
            if (page == '...') {
                return;
            }
            this.currentPage = parseInt(page);
            this.$emit('input', page);
        },
        openPreviousPage: function () {
            if (this.currentPage !== 1) {
                this.openPage(this.currentPage - 1);
            }
        },
        openNextPage: function () {
            if (this.currentPage < this.pages) {
                this.openPage(this.currentPage + 1);
            }
        },
        openFirstPage: function () {
            this.openPage(1);
        },
        openLastPage: function () {
            this.openPage(this.pages);
        }
    },
};
</script>