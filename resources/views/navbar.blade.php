<nav class="h-16 flex justify-start mb-3">
    <div class="flex justify-between items-center w-full">
        <button @click.stop="sidebarOpen = !sidebarOpen; mobileSidebarOpen = !mobileSidebarOpen" class="button bg-gray-300 text-gray-800 dark:bg-gray-700 dark:text-gray-200 z-20 h-auto w-12 inline-flex items-center justify-center rounded-md mx-2">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <div class="w-full">
            <search />
        </div>
        <user-dropdown photo="{{ Voyager::assetUrl('images/default-avatar.png') }}" name="{{ $authentication->name() }}" />
    </div>
</nav>