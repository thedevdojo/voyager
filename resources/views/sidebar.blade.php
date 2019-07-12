<aside class="flex flex-col h-full min-h-screen aside-expand w-56 pr-5">

    <div class="flex justify-left pl-6 items-center h-20 logo">
        @include('voyager::partials.logo')
        <span class="font-black ml-3 text-gray-800 text-lg uppercase">Voyager</span>
    </div>

    <div class="flex-1 text-gray-600">
        <ul class="ml-6">
            <li>
                <a href="/admin" class="flex items-center no-underline">
                
                    <span class="ml-3 font-hairline"> Dashboard </span>
                </a>
            </li>

            <li>
                <a href="/admin/users" class="flex items-center no-underline">
                    <span class="ml-3 font-hairline"> Users </span>
                </a>
            </li>

            <li>
                <a href="/admin/posts" class="flex items-center no-underline">
                    <span class="ml-3 font-hairline"> Posts </span>
                </a>
            </li>

            <li>
                <a href="/admin/pages" class="flex items-center no-underline">
                    <span class="ml-3 font-hairline"> Pages </span>
                </a>
            </li>

            <li>
                <a href="/admin/devices" class="flex items-center no-underline">
                    <span class="ml-3 font-hairline"> Managed Devices </span>
                </a>
            </li>

        </ul>
    </div>

</aside>
