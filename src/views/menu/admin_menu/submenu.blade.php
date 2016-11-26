<li class="dropdown">
    <a data-toggle="collapse" href="#sub-dropdown-element"><span class="icon {{ $item->icon_class }}"></span><span class="title">{{ $item->title }}</span></a>
    <div id="sub-dropdown-element" class="panel-collapse collapse">
        <div class="panel-body">
            <ul class="nav navbar-nav">
                @each('voyager::menu.admin_menu.item', $item->subitems, 'item')
            </ul>
        </div>
    </div>
</li>