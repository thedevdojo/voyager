<template>
    <div>
        <label class="block text-gray-700 text-sm font-bold mb-2">Slug</label>
        <language-input
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                type="text" placeholder="Slug"
                v-bind:value="bread.slug"
                v-on:input="bread.slug = $event" />

        <label class="block text-gray-700 text-sm font-bold mb-2">Name singular</label>
        <language-input
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                type="text" placeholder="Name singular"
                v-bind:value="bread.name_singular"
                v-on:input="bread.name_singular = $event" />

        <label class="block text-gray-700 text-sm font-bold mb-2">Name plural</label>
        <language-input
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                type="text" placeholder="Name plural"
                v-bind:value="bread.name_plural"
                v-on:input="bread.name_plural = $event" />
    </div>
</template>

<script>
export default {
    props: ['bread']
};
</script>