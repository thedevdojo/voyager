<template>
    <div>
        <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('voyager::generic.slug') }}</label>
        <language-input
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            type="text" :placeholder="__('voyager::generic.slug')"
            v-bind:value="bread.slug"
            v-on:input="bread.slug = $event" />

        <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('voyager::bread.name_singular') }}</label>
        <language-input
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            type="text" :placeholder="__('voyager::bread.name_singular')"
            v-bind:value="bread.name_singular"
            v-on:input="bread.name_singular = $event" />

        <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('voyager::bread.name_plural') }}</label>
        <language-input
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            type="text" :placeholder="__('voyager::bread.name_plural')"
            v-bind:value="bread.name_plural"
            v-on:input="bread.name_plural = $event" />
        <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('voyager::bread.model_name') }}</label>
        <input
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            type="text" :placeholder="__('voyager::bread.model_name')"
            v-model="bread.model_name" />
        <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('voyager::bread.controller') }}</label>
        <input
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            type="text" :placeholder="__('voyager::bread.controller')"
            v-model="bread.controller" />
        <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('voyager::bread.policy') }}</label>
        <input
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            type="text" :placeholder="__('voyager::bread.policy')"
            v-model="bread.policy" />

        <div class="text-right">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" @click="saveBread()">{{ __('voyager::generic.save') }}</button>
        </div>
    </div>
</template>

<script>
export default {
    props: ['bread', 'url'],
    methods: {
        saveBread: function () {
            var vm = this;
            axios.put(this.url, {
                bread: vm.bread
            })
            .then(function (response) {
                if (response.data.success) {
                    vm.$snotify.success(response.data.message);
                } else {
                    vm.$snotify.error(response.data.message);
                }
            })
            .catch(function (error) {
                vm.$snotify.error(error);
            });
        }
    },
};
</script>