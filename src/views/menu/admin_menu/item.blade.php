@if ($item->isSubMenu())
    @include('voyager::menu.admin_menu.submenu', ['item' => $item])
@else
    @include('voyager::menu.admin_menu.link', ['item' => $item])
@endif