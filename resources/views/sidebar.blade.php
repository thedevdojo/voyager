<aside class="flex flex-col justify-between h-full min-h-screen h-full aside-expand w-56 bg-gray-100 dark:bg-gray-800 overflow-x-hidden fixed">
    <div class="flex flex-col justify-start">
        <a :href="route('voyager.dashboard')" class="flex justify-start pl-8 items-center w-auto h-16 relative text-black dark:text-white">
            <i class="uil uil-sun"></i>
            <span class="font-black text-lg uppercase pl-2">Voyager</span>
        </a>
        @php
            $menu_plugin = Voyager::getPluginByType('menu');
        @endphp

        @if ($menu_plugin && $menu_plugin->getMenuView())
            
        @else
        <nav class="text-gray-500 px-5 mt-4">
            <p class="text-xs font-medium uppercase ml-3 mb-4">{{ __('voyager::generic.main') }}</p>
            <a :href="route('voyager.dashboard')" class="flex justify-start items-center no-underline h-10 pl-2 relative bg-black dark:bg-white rounded-full  text-white dark:text-black">                   
                <i class="uil uil-dashboard text-xl"></i>
                <span class="text-white dark:text-black text-xs font-medium leading-none">{{ __('voyager::generic.dashboard') }}</span>
            </a>

            <a href="{{ route('voyager.bread.index') }}" class="flex justify-start items-center no-underline h-10 pl-2 relative rounded-full mt-2 text-black dark:text-white">
                <i class="uil uil-dashboard text-xl"></i>
                <span class="text-xs font-medium leading-none">{{ __('voyager::manager.breads') }}</span>
            </a>

            <a href="{{ route('voyager.ui') }}" class="flex justify-start items-center no-underline h-10 pl-2 relative rounded-full mt-2 text-black dark:text-white">
                <i class="uil uil-window text-xl"></i>
                <span class="text-xs font-medium leading-none">{{ __('voyager::generic.ui_components') }}</span>
            </a>

            <a href="{{ route('voyager.settings.index') }}" class="flex justify-start items-center no-underline h-10 pl-2 relative rounded-full mt-2 text-black dark:text-white">
                <i class="uil uil-cog text-xl"></i>
                <span class="text-xs font-medium leading-none">{{ __('voyager::generic.settings') }}</span>
            </a>

            <a href="{{ route('voyager.plugins.index') }}" class="flex justify-start items-center no-underline h-10 pl-2 relative rounded-full mt-2 text-black dark:text-white">
                <i class="uil uil-layer-group text-xl"></i>
                <span class="text-xs font-medium leading-none">{{ __('voyager::plugins.plugins') }}</span>
            </a>
            @if (count(Bread::getBreads()) > 0)
                <p class="text-xs font-medium uppercase ml-3 mb-2 mt-8">{{ __('voyager::manager.breads') }}</p>
                @foreach (Bread::getBreads() as $bread)
                <a href="{{ route('voyager.'.$bread->slug.'.browse') }}" class="flex justify-start items-center no-underline h-10 pl-2 relative rounded-full mt-2 text-black dark:text-white">
                    <i class="uil text-xl uil-{{ $bread->icon }}"></i>
                    <span class="text-xs font-medium leading-none">
                        {{ $bread->name_plural }}
                    </span>
                </a>
                @endforeach
            @endif
        </nav>
        @endif
    </div>

    <div class="flex items-center">
        <button class="rounded-full bg-gray-300 dark:bg-gray-700 focus:outline-none outline-none px-2 py-2 rounded inline-flex items-center" @click="$globals.toggleDarkMode">
            <i class="uil text-xl" :class="[$globals.darkmode ? 'uil-sun' : 'uil-moon']"></i>
        </button>
        <img src="{{ Voyager::assetUrl('images/default-avatar.png') }}" class="rounded-full m-4 w-8" alt="User Avatar">
        <locale-picker v-if="$language.localePicker" />
    </div>
</aside>
