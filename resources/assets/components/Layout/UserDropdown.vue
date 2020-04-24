<template>
    <div class="mt-1 text-right max-w-sm mx-auto z-30">
        <dropdown>
            <div>
                <div class="flex items-center px-6 py-4">
                    <img class="h-10 w-10 rounded-full flex-no-shrink" :src="photo" alt="">
                    <div class="ltr:ml-4 rtl:mr-4">
                    <p class="font-semibold text-gray-900 dark:text-gray-100 leading-none">{{ name }}</p>
                    <p>
                        <a href="#" class="text-sm text-gray-600 dark:text-gray-400 leading-none hover:underline">
                            {{ __('voyager::generic.view_profile') }}
                        </a>
                    </p>
                    </div>
                </div>
                <a :href="route('voyager.dashboard')" class="link">
                    {{ __('voyager::generic.dashboard') }}
                </a>
                <a :href="route('voyager.settings.index')" class="link">
                    {{ __('voyager::generic.settings') }}
                </a>
                <a :href="route('voyager.logout')" class="link">
                    {{ __('voyager::auth.logout') }}
                </a>
            </div>
            <div slot="opener">
                <button class="flex items-center pl-6 py-2 font-semibold rounded-lg focus:outline-none">
                    <div class="w-48 text-right text-sm flex justify-end">
                        <div class="w-6 h-6 rounded-full overflow-hidden bg-black flex justify-center items-center relative">
                            <div class="w-3 h-3 rounded-full bg-white border-black relative z-10"></div>
                            <div class="w-5 h-5 rounded-full bottom-0 absolute bg-gray-500 -mb-3"></div>
                        </div>
                        <span class="hidden md:block ltr:ml-3 rtl:mr-3">
                            Hello {{ name }}
                        </span>
                    </div>
                
                    <svg class="ml-2 h-6 w-6 text-black dark:text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M15.3 9.3a1 1 0 0 1 1.4 1.4l-4 4a1 1 0 0 1-1.4 0l-4-4a1 1 0 0 1 1.4-1.4l3.3 3.29 3.3-3.3z"/>
                    </svg>
                </button>
            </div>
        </dropdown>
    </div>
</template>

<script>
export default {
    props: ['photo', 'name'],
};
</script>