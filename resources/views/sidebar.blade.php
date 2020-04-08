
<!-- Mobile sidebar -->
<div v-if="mobileSidebarOpen" class="md:hidden">
    <div class="fixed inset-0 z-30">
        <div class="absolute inset-0 bg-gray-600 opacity-75"></div>
    </div>
    <div class="fixed inset-0 flex z-40" @click="mobileSidebarOpen = false">
        <div class="flex-1 flex flex-col max-w-xs w-full sidebar" @click.stop="">
            <div class="absolute top-0 right-0 p-1">
                <button @click="mobileSidebarOpen = false" class="flex items-center justify-center h-12 w-12 rounded-full">
                    <i class="uil uil-times text-3xl"></i>
                </button>
            </div>
            <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                <div class="flex-shrink-0 flex items-center px-4">
                    <helm class="w-10 h-10 icon"></helm>
                    <span class="font-black text-lg uppercase pl-2 title">Voyager</span>
                </div>
                @php
                    $menu_plugin = VoyagerPlugins::getPluginByType('menu');
                    $current_url = Str::finish('/', url()->current());
                @endphp

                @if ($menu_plugin && $menu_plugin->getMenuView())
                    {!! $menu_plugin->getMenuView()->with(['mobile' => true])->render() !!}
                @else
                <nav class="mt-3 px-2">
                <a :href="route('voyager.dashboard')" class="text-base leading-6 item {{ $current_url == Str::finish('/', route('voyager.dashboard')) ? 'active' : '' }}">                   
                        <i class="uil uil-dashboard text-2xl icon"></i>
                        {{ __('voyager::generic.dashboard') }}
                    </a>

                    <a href="{{ route('voyager.bread.index') }}" class="text-base leading-6 item {{ Str::startsWith($current_url, Str::finish('/', route('voyager.bread.index'))) ? 'active' : '' }}">
                        <i class="uil uil-dashboard text-2xl icon"></i>
                        {{ __('voyager::generic.breads') }}
                    </a>

                    <a href="{{ route('voyager.ui') }}" class="text-base leading-6 item {{ $current_url == Str::finish('/', route('voyager.ui')) ? 'active' : '' }}">
                        <i class="uil uil-window text-2xl icon"></i>
                        {{ __('voyager::generic.ui_components') }}
                    </a>

                    <a href="{{ route('voyager.settings.index') }}" class="text-base leading-6 item {{ Str::startsWith($current_url, Str::finish('/', route('voyager.settings.index'))) ? 'active' : '' }}">
                        <i class="uil uil-cog text-2xl icon"></i>
                        {{ __('voyager::generic.settings') }}
                    </a>

                    <a href="{{ route('voyager.plugins.index') }}" class="mb-5 text-base leading-6 item {{ Str::startsWith($current_url, Str::finish('/', route('voyager.plugins.index'))) ? 'active' : '' }}">
                        <i class="uil uil-layer-group text-2xl icon"></i>
                        {{ __('voyager::plugins.plugins') }}
                    </a>
                    @if (count(Bread::getBreads()) > 0)
                        @foreach (Bread::getBreads() as $bread)
                        <a href="{{ route('voyager.'.$bread->slug.'.browse') }}" class="text-base leading-6 item {{ Str::startsWith($current_url, Str::finish('/', route('voyager.'.$bread->slug.'.browse'))) ? 'active' : '' }}">
                            <i class="uil text-2xl uil-{{ $bread->icon }} icon"></i>
                            {{ $bread->name_plural }}
                        </a>
                        @endforeach
                    @endif
                </nav>
                @endif
            </div>
            <div class="flex-shrink-0 flex border-t sidebar-border p-4">
                <button class="rounded-full bg-gray-300 dark:bg-gray-700 outline-none px-2 py-2 rounded inline-flex items-center" @click="$globals.toggleDarkMode">
                    <i class="uil text-xl" :class="[$globals.darkmode ? 'uil-sun' : 'uil-moon']"></i>
                </button>
                <img src="{{ Voyager::assetUrl('images/default-avatar.png') }}" class="rounded-full m-4 w-8" alt="User Avatar">
                <locale-picker v-if="$language.localePicker" />
            </div>
        </div>
        <div class="flex-shrink-0 w-14"></div>
    </div>
</div>

<!-- Desktop sidebar -->
<slide-x-left-transition>
    <div class="hidden md:flex md:flex-shrink-0 sidebar" v-if="sidebarOpen">
        <div class="flex flex-col w-64 border-r sidebar-border">
            <div class="h-0 flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                <div class="flex items-center flex-shrink-0 px-4">
                    <helm class="w-10 h-10 icon"></helm>
                    <span class="font-black text-lg uppercase pl-2 title">Voyager</span>
                </div>
                @php
                    $menu_plugin = VoyagerPlugins::getPluginByType('menu');
                    $current_url = Str::finish('/', url()->current());
                @endphp

                @if ($menu_plugin && $menu_plugin->getMenuView())
                    {!! $menu_plugin->getMenuView()->with(['mobile' => false])->render() !!}
                @else
                <nav class="mt-4 flex-1 px-2">
                    <a :href="route('voyager.dashboard')" class="text-sm leading-5 item {{ $current_url == Str::finish('/', route('voyager.dashboard')) ? 'active' : '' }}">                   
                        <icon icon="dashboard" class="icon mr-2"></icon>
                        {{ __('voyager::generic.dashboard') }}
                    </a>

                    <a href="{{ route('voyager.bread.index') }}" class="text-sm leading-5 item {{ Str::startsWith($current_url, Str::finish('/', route('voyager.bread.index'))) ? 'active' : '' }}">
                        <icon icon="dashboard" class="icon mr-2"></icon>
                        {{ __('voyager::generic.breads') }}
                    </a>

                    <a href="{{ route('voyager.ui') }}" class="text-sm leading-5 item {{ $current_url == Str::finish('/', route('voyager.ui')) ? 'active' : '' }}">
                        <icon icon="window" class="icon mr-2"></icon>
                        {{ __('voyager::generic.ui_components') }}
                    </a>

                    <a href="{{ route('voyager.settings.index') }}" class="text-sm leading-5 item {{ Str::startsWith($current_url, Str::finish('/', route('voyager.settings.index'))) ? 'active' : '' }}">
                        <icon icon="cog" class="icon mr-2"></icon>
                        {{ __('voyager::generic.settings') }}
                    </a>

                    <a href="{{ route('voyager.plugins.index') }}" class="text-sm leading-5 item {{ Str::startsWith($current_url, Str::finish('/', route('voyager.plugins.index'))) ? 'active' : '' }}">
                    <icon icon="layer-group" class="icon mr-2"></icon>
                        {{ __('voyager::plugins.plugins') }}
                    </a>
                    @if (count(Bread::getBreads()) > 0)
                        <hr class="my-3 sidebar-border" />
                        @foreach (Bread::getBreads() as $bread)
                        <a href="{{ route('voyager.'.$bread->slug.'.browse') }}" class="text-sm leading-5 item {{ Str::startsWith($current_url, Str::finish('/', route('voyager.'.$bread->slug.'.browse'))) ? 'active' : '' }}">
                            <icon icon="{{ $bread->icon }}" class="icon mr-2"></icon>
                            {{ $bread->name_plural }}
                        </a>
                        @endforeach
                    @endif
                </nav>
                @endif
            </div>
            <div class="flex-shrink-0 flex border-t sidebar-border p-4 h-auto">
                <button class="rounded-full bg-gray-300 dark:bg-gray-700 outline-none px-2 py-2 rounded inline-flex items-center" @click="$globals.toggleDarkMode">
                    <icon :icon="$globals.darkmode ? 'sun' : 'moon'" />
                </button>
                <img src="{{ Voyager::assetUrl('images/default-avatar.png') }}" class="rounded-full m-4 w-8" alt="User Avatar">
                <locale-picker v-if="$language.localePicker" />
            </div>
        </div>
    </div>
</slide-x-left-transition>