<aside class="flex flex-col justify-between h-full min-h-screen h-full aside-expand w-56 bg-gray-100 dark:bg-gray-800 overflow-x-hidden fixed">
    <div class="flex flex-col justify-start">
        <a :href="route('voyager.dashboard')" class="flex justify-start pl-8 items-center w-auto h-16 relative text-white">
            <unicon name="helm" view-box="0 0 600 600" fill="currentColor"></unicon>
            <span class="font-black text-black dark:text-white text-lg uppercase pl-2">Voyager</span>
        </a>

        <nav class="text-gray-500 px-5 mt-4">
            <p class="text-xs font-medium uppercase ml-3 mb-4">MAIN</p>
            <a :href="route('voyager.dashboard')" class="flex justify-start items-center no-underline h-10 pl-2 relative bg-black dark:bg-white rounded-full  text-white dark:text-black">                   
                <unicon name="dashboard" fill="currentColor" class="pr-2" height="20" width="20"></unicon>
                <span class="text-white dark:text-black text-xs font-medium leading-none">Dashboard</span>
            </a>

            <a href="{{ route('voyager.bread.index') }}" class="flex justify-start items-center no-underline h-10 pl-2 relative rounded-full mt-2 text-black dark:text-white">
                <unicon name="bread" fill="currentColor" view-box="0 0 512 512" class="pr-2" height="20" width="20"></unicon>
                <span class="text-xs font-medium leading-none"> {{ __('voyager::manager.breads') }} </span>
            </a>

            <a href="{{ route('voyager.ui') }}" class="flex justify-start items-center no-underline h-10 pl-2 relative rounded-full mt-2 text-black dark:text-white">
                <unicon name="window" fill="currentColor" class="pr-2" height="20" width="20"></unicon>
                <span class="text-xs font-medium leading-none"> UI Components </span>
            </a>

            <a href="{{ route('voyager.settings.index') }}" class="flex justify-start items-center no-underline h-10 pl-2 relative rounded-full mt-2 text-black dark:text-white">
                <unicon name="cog" fill="currentColor" class="pr-2" height="20" width="20"></unicon>
                <span class="text-xs font-medium leading-none"> Settings </span>
            </a>

            <a href="{{ route('voyager.plugins.index') }}" class="flex justify-start items-center no-underline h-10 pl-2 relative rounded-full mt-2 text-black dark:text-white">
                <unicon name="layer-group" fill="currentColor" class="pr-2" height="20" width="20"></unicon>
                <span class="text-xs font-medium leading-none"> Plugins </span>
            </a>

            <p class="text-xs font-medium uppercase ml-3 mb-2 mt-12">DATA</p>
            <a href="{{ route('voyager.ui') }}" class="flex justify-start items-center no-underline h-10 pl-2 relative rounded-full mt-2 text-black dark:text-white">
                <unicon name="users-alt" fill="currentColor" class="pr-2" height="20" width="20"></unicon>
                <span class="text-xs font-medium leading-none"> Users </span>
            </a>
        </nav>
    </div>

    <div class="flex items-center">
        <button class="rounded-full bg-gray-300 dark:bg-gray-700 focus:outline-none outline-none px-2 py-2 rounded inline-flex items-center" @click="$globals.toggleDarkMode">
            <unicon :name="$globals.darkmode ? 'sun' : 'moon'" fill="currentColor" />
        </button>
        <img src="{{ Voyager::assetUrl('images/default-avatar.png') }}" class="rounded-full m-4 w-8" alt="User Avatar">
    </div>
</aside>
