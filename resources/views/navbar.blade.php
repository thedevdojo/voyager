<nav class="h-16 flex justify-start mb-3 mx-auto sm:px-3 md:px-4">
    <div class="flex justify-between items-center w-full">
        <button @click.stop="$store.toggleSidebar()" class="button gray small icon-only mx-2">
            <icon :icon="$store.sidebarOpen ? 'ellipsis-v' : 'ellipsis-h'" />
        </button>
        <div class="w-full mt-4">
            <search placeholder="{{ Bread::getBreadSearchPlaceholder() }}" :mobile-placeholder="__('voyager::generic.search')" />
        </div>
        <user-dropdown photo="{{ Voyager::assetUrl('images/default-avatar.png') }}" name="{{ $authentication->name() }}" />
    </div>
</nav>