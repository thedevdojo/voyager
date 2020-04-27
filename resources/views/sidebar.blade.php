
<!-- Mobile sidebar -->
<div v-if="$store.sidebarOpen" class="md:hidden" :key="'mobile_sidebar'">
    <div class="fixed inset-0 z-30">
        <div class="absolute inset-0 bg-gray-600 opacity-75"></div>
    </div>
    <div class="fixed inset-0 flex z-40" @click="$store.toggleSidebar()">
        <div class="flex-1 flex flex-col max-w-xs w-full sidebar" @click.stop="">
            <div class="absolute top-0 right-0 p-1">
                <button @click="$store.toggleSidebar()" class="flex items-center justify-center h-12 w-12 rounded-full">
                    <icon icon="times"></icon>
                </button>
            </div>
            <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                <div class="flex-shrink-0 flex items-center px-4">
                    <icon icon="helm" :size="10" class="icon"></icon>
                    <span class="font-black text-lg uppercase pl-2 title">Voyager</span>
                </div>
                @php
                    $menu_plugin = VoyagerPlugins::getPluginByType('menu');
                    $current_url = Str::finish(url()->current(), '/');
                @endphp

                @if ($menu_plugin && $menu_plugin->getMenuView())
                    {!! $menu_plugin->getMenuView()->with(['mobile' => true])->render() !!}
                @else
                <nav class="mt-3 px-2">
                <menu-item
                    :title="__('voyager::generic.dashboard')"
                    :href="route('voyager.dashboard')"
                    icon="dashboard" 
                    {{ $current_url == Str::finish(route('voyager.dashboard'), '/') ? 'active' : '' }}>
                </menu-item>

                <menu-item
                    :title="__('voyager::generic.breads')"
                    :href="route('voyager.bread.index')"
                    icon="bread" 
                    {{ Str::startsWith($current_url, Str::finish(route('voyager.bread.index'), '/')) ? 'active' : '' }}>
                    @if (count(Bread::getBreads()) > 0)
                        <div>
                        @foreach (Bread::getBreads() as $bread)
                            @php
                                $active = Str::startsWith($current_url, route('voyager.bread.edit', $bread->table));
                            @endphp
                            <menu-item
                                title="{{ $bread->name_plural }}"
                                href="{{ route('voyager.bread.edit', $bread->table) }}"
                                icon="{{ $bread->icon }}" 
                                {{ $active ? 'active' : '' }}>
                            </menu-item>
                        @endforeach
                        </div>
                    @endif
                </menu-item>

                <menu-item
                    :title="__('voyager::generic.media')"
                    :href="route('voyager.media')"
                    icon="film" 
                    {{ $current_url == Str::finish(route('voyager.media'), '/') ? 'active' : '' }}>
                </menu-item>

                <menu-item
                    :title="__('voyager::generic.ui_components')"
                    :href="route('voyager.ui')"
                    icon="window" 
                    {{ $current_url == Str::finish(route('voyager.ui'), '/') ? 'active' : '' }}>
                </menu-item>

                <menu-item
                    :title="__('voyager::generic.settings')"
                    :href="route('voyager.settings.index')"
                    icon="cog" 
                    {{ Str::startsWith($current_url, Str::finish(route('voyager.settings.index'), '/')) ? 'active' : '' }}>
                </menu-item>
                
                <menu-item
                    :title="__('voyager::plugins.plugins')"
                    :href="route('voyager.plugins.index')"
                    icon="puzzle-piece" 
                    {{ Str::startsWith($current_url, Str::finish(route('voyager.plugins.index'), '/')) ? 'active' : '' }}>
                </menu-item>

                @if (count(Bread::getBreads()) > 0)
                    <hr class="my-3 sidebar-border" />
                    @foreach (Bread::getBreads() as $bread)
                    @php
                        $active = Str::startsWith($current_url, Str::finish(route('voyager.'.$bread->slug.'.browse'), '/'));
                    @endphp
                    <menu-item
                        title="{{ $bread->name_plural }}"
                        href="{{ route('voyager.'.$bread->slug.'.browse') }}"
                        icon="{{ $bread->icon }}"
                        @if (isset($bread->badge) && $bread->badge)
                            :badge="true"
                            badge-content="{{ $bread->getReadableCount() }}"
                            badge-color="{{ isset($bread->color) ? $bread->color : 'green' }}"
                        @endif
                        {{ $active ? 'active' : '' }}>
                    </menu-item>
                    @endforeach
                @endif
                </nav>
                @endif
            </div>
            <div class="flex-shrink-0 flex border-t sidebar-border p-4">
                <button class="rounded-full bg-gray-300 dark:bg-gray-700 outline-none px-2 py-2 rounded inline-flex items-center" @click="$store.toggleDarkMode()">
                    <icon :icon="$store.darkmode ? 'sun' : 'moon'"></icon>
                </button>
                <img src="{{ Voyager::assetUrl('images/default-avatar.png') }}" class="rounded-full m-4 w-8" alt="User Avatar">
            </div>
        </div>
        <div class="flex-shrink-0 w-14"></div>
    </div>
</div>

<!-- Desktop sidebar -->
<div class="hidden md:flex md:flex-shrink-0 sidebar h-full" v-if="$store.sidebarOpen" :key="'desktop_sidebar'">
    <div class="flex flex-col w-64 border-r sidebar-border">
        <div class="h-0 flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
            <div class="flex items-center flex-shrink-0 px-4">
                <icon icon="helm" :size="10" class="icon"></icon>
                <span class="font-black text-lg uppercase ltr:pl-2 rtl:pr-2 title">Voyager</span>
            </div>
            @php
                $menu_plugin = VoyagerPlugins::getPluginByType('menu');
                $current_url = Str::finish(url()->current(), '/');
            @endphp

            @if ($menu_plugin && $menu_plugin->getMenuView())
                {!! $menu_plugin->getMenuView()->with(['mobile' => false])->render() !!}
            @else
            <nav class="mt-4 flex-1 px-2">
                <menu-item
                    :title="__('voyager::generic.dashboard')"
                    :href="route('voyager.dashboard')"
                    icon="dashboard" 
                    {{ $current_url == Str::finish(route('voyager.dashboard'), '/') ? 'active' : '' }}>
                </menu-item>

                <menu-item
                    :title="__('voyager::generic.breads')"
                    :href="route('voyager.bread.index')"
                    icon="bread" 
                    {{ Str::startsWith($current_url, Str::finish(route('voyager.bread.index'), '/')) ? 'active' : '' }}>
                    @if (count(Bread::getBreads()) > 0)
                        <div>
                        @foreach (Bread::getBreads() as $bread)
                            @php
                                $active = Str::startsWith($current_url, Str::finish(route('voyager.bread.edit', $bread->table), '/'));
                            @endphp
                            <menu-item
                                title="{{ $bread->name_plural }}"
                                href="{{ route('voyager.bread.edit', $bread->table) }}"
                                icon="{{ $bread->icon }}" 
                                {{ $active ? 'active' : '' }}>
                            </menu-item>
                        @endforeach
                        </div>
                    @endif
                </menu-item>

                <menu-item
                    :title="__('voyager::generic.media')"
                    :href="route('voyager.media')"
                    icon="film" 
                    {{ $current_url == Str::finish(route('voyager.media'), '/') ? 'active' : '' }}>
                </menu-item>

                <menu-item
                    :title="__('voyager::generic.ui_components')"
                    :href="route('voyager.ui')"
                    icon="window" 
                    {{ $current_url == Str::finish(route('voyager.ui'), '/') ? 'active' : '' }}>
                </menu-item>

                <menu-item
                    :title="__('voyager::generic.settings')"
                    :href="route('voyager.settings.index')"
                    icon="cog" 
                    {{ Str::startsWith($current_url, Str::finish(route('voyager.settings.index'), '/')) ? 'active' : '' }}>
                </menu-item>
                
                <menu-item
                    :title="__('voyager::plugins.plugins')"
                    :href="route('voyager.plugins.index')"
                    icon="puzzle-piece" 
                    {{ Str::startsWith($current_url, Str::finish(route('voyager.plugins.index'), '/')) ? 'active' : '' }}>
                </menu-item>

                @if (count(Bread::getBreads()) > 0)
                    <hr class="my-3 sidebar-border" />
                    @foreach (Bread::getBreads() as $bread)
                    @php
                        $active = Str::startsWith($current_url, Str::finish(route('voyager.'.$bread->slug.'.browse'), '/'));
                    @endphp
                    <menu-item
                        title="{{ $bread->name_plural }}"
                        href="{{ route('voyager.'.$bread->slug.'.browse') }}"
                        icon="{{ $bread->icon }}"
                        @if (isset($bread->badge) && $bread->badge)
                            :badge="true"
                            badge-content="{{ $bread->getReadableCount() }}"
                            badge-color="{{ isset($bread->color) ? $bread->color : 'green' }}"
                            :badge-dot="{{ $active ? 'true' : 'false' }}"
                        @endif
                        {{ $active ? 'active' : '' }}>
                    </menu-item>
                    @endforeach
                @endif
            </nav>
            @endif
        </div>
        <div class="flex-shrink-0 inline-flex border-t sidebar-border p-4 h-auto overflow-x-hidden">
            <button class="button blue small icon-only" @click="$store.toggleDarkMode()">
                <icon :icon="$store.darkmode ? 'sun' : 'moon'" />
            </button>
            <button class="button blue small icon-only" v-scroll-to="{ el: '#scroll-top', offset: -200 }">
                <icon icon="arrow-circle-up" />
            </button>
            <button class="button blue small icon-only" @click="$store.toggleDirection()">
                <icon :icon="$store.rtl ? 'left-to-right-text-direction' : 'right-to-left-text-direction'" />
            </button>
        </div>
    </div>
</div>