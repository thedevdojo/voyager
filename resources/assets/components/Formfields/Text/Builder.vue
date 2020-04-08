<template>
    <div>
        <div v-if="show == 'list-options'">
            <label for="length" class="label">Display length</label>
            <input type="text" id="length" class="voyager-input w-full" v-model="options.display_length"> 
        </div>
        <div v-else-if="show == 'view-options'">
            <label class="label mt-4">Label</label>
            <language-input
                class="voyager-input w-full"
                type="text" placeholder="Label"
                v-bind:value="options.label"
                v-on:input="options.label = $event" />

            <label class="label mt-4">Description</label>
            <language-input
                class="voyager-input w-full"
                type="text" placeholder="Description"
                v-bind:value="options.description"
                v-on:input="options.description = $event" />

            <label class="label mt-4">Placeholder</label>
            <language-input
                class="voyager-input w-full"
                type="text" placeholder="Placeholder"
                v-bind:value="options.placeholder"
                v-on:input="options.placeholder = $event" /> 

            <label class="label mt-4">Default value</label>
            <language-input
                class="voyager-input w-full"
                type="text" placeholder="Default value"
                v-bind:value="options.default_value"
                v-on:input="options.default_value = $event" /> 

            <label class="label mt-4">Rows</label>
            <input type="number" min="1" max="1000" class="voyager-input w-full" v-model="options.rows"> 
        </div>
        <div v-else-if="show == 'view'">
            <label class="label" v-if="translate(options.label) !== ''">{{ translate(options.label) }}</label>
            <input
                v-if="options.rows == 1"
                type="text"
                class="voyager-input w-full"
                v-bind:value="translate(options.default_value)"
                :placeholder="translate(options.placeholder)">
            <textarea
                v-else
                class="voyager-input w-full"
                :rows="options.rows"
                v-bind:value="translate(options.default_value)"
                :placeholder="translate(options.placeholder)"></textarea>
            <p class="description" v-if="translate(options.description) !== ''">
                {{ translate(options.description) }}
            </p>
        </div>
    </div>
</template>

<script>
export default {
    props: ['options', 'column', 'show'],
};
</script>