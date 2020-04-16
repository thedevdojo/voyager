<template>
    <div>
        <div v-if="show == 'list-options'">
            
        </div>
        <div v-else-if="show == 'view-options'">
            <label class="label mt-4">Items minimum</label>
            <input type="number" min="0" max="999999" class="voyager-input w-full" v-model="options.min"> 

            <label class="label mt-4">Items maximum</label>
            <input type="number" min="0" max="999999" class="voyager-input w-full" v-model="options.max"> 

            <label class="label mt-4">Add text</label>
            <language-input
                class="voyager-input w-full"
                type="text" placeholder="Add text"
                v-bind:value="options.add_text"
                v-on:input="options.add_text = $event" /> 

            <label class="label mt-4">Remove text</label>
            <language-input
                class="voyager-input w-full"
                type="text" placeholder="Remove text"
                v-bind:value="options.remove_text"
                v-on:input="options.remove_text = $event" />
        </div>
        <div v-else-if="show == 'view'">
            <bread-manager-view
                :formfields="options.formfields"
                v-on:formfields="options.formfields = $event"
                v-on:delete="deleteFormfield($event)"
                v-on:open-options="openOptions($event)">
                <div class="w-full text-center text-2xl my-8">
                    <h2>Drag and drop your formfields here</h2>
                </div>
            </bread-manager-view>
            <div class="w-full">
                <button class="button green w-full justify-center" disabled>
                    {{ translate(options.add_text) }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['options', 'column', 'show'],

    methods: {
         deleteFormfield: function (key) {
            var vm = this;

            // TODO
        },
        openOptions: function (key) {
            this.$emit('open-options', this._uid + '_' + key);
        }
    },
};
</script>