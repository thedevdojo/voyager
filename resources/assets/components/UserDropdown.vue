<template>
    <div class="mt-1 text-right max-w-sm mx-auto">
        <div id="user_dropdown" class="relative inline-block">
            <button @click="toggle" class="flex items-center pl-6 py-2 font-semibold rounded-lg focus:outline-none">
            
            <div class="w-48 text-right text-sm flex justify-end">
                <div class="w-6 h-6 rounded-full overflow-hidden bg-black flex justify-center items-center relative mr-3"><div class="w-3 h-3 rounded-full bg-white border-black relative z-10"></div> <div class="w-5 h-5 rounded-full bottom-0 absolute bg-gray-500 -mb-3"></div></div>
                Hello Anonymous
            </div>
           
            <svg class="ml-2 h-6 w-6 text-black" fill="currentColor" viewBox="0 0 24 24">
                <path d="M15.3 9.3a1 1 0 0 1 1.4 1.4l-4 4a1 1 0 0 1-1.4 0l-4-4a1 1 0 0 1 1.4-1.4l3.3 3.29 3.3-3.3z"/>
            </svg>
            </button>
            <transition
            enter-class="opacity-0 scale-90"
            enter-active-class="ease-out transition-fastest"
            enter-to-class="opacity-100 scale-100"
            leave-class="opacity-100 scale-100"
            leave-active-class="ease-in transition-fastest"
            leave-to-class="opacity-0 scale-90"
            >
            <!-- Replace "right-0 origin-top-right" with "left-0 origin-top-left" to left-align the dropdown -->
            <div v-if="isOpen" class="mt-2 absolute right-0 origin-top-right">
                <div class="w-64 text-left bg-white rounded-lg shadow-lg">
                <div class="flex items-center px-6 py-4">
                    <img class="h-10 w-10 rounded-full flex-no-shrink" :src="photo" alt="">
                    <div class="ml-4">
                    <p class="font-semibold text-gray-900 leading-none">{{ name }}</p>
                    <p><a href="#" class="text-sm text-gray-600 leading-none hover:underline">View Profile</a></p>
                    </div>
                </div>
                <div class="border-t-2 border-gray-100 py-1">
                    <a href="/" class="block px-6 py-3 leading-tight hover:bg-gray-200">Home</a>
                    <a href="#" class="block px-6 py-3 leading-tight hover:bg-gray-200">Settings</a>
                </div>
                <form class="border-t-2 border-gray-100 py-1" action="/logout" method="POST">
                    <button class="block w-full px-6 py-3 text-left leading-tight hover:bg-gray-200">Sign out</button>
                </form>
                </div>
            </div>
            </transition>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['photo', 'name'],
        data: function () {
            return {
                isOpen: false,
            };
        },
        methods: {
            toggle() {
                this.isOpen = !this.isOpen
            },
        },
    };
</script>