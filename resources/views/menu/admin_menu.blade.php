<ul class="nav navbar-nav">

@php
    if (Voyager::translatable($items)) {
        $items = $items->load('translations');
    }
@endphp

@foreach ($items as $item)
    @php
        $listItemClass = [];
        $styles = null;
        $linkAttributes = null;

        if (Voyager::translatable($item)) {
            $item = $item->translate($options->locale);
        }

        $href = $item->link();

        // Current page
        if(url($href) == url()->current()) {
            array_push($listItemClass, 'active');
        }

        $permission = '';

        // With Children Attributes
        if(!$item->children->isEmpty())
        {
            $hasChildren = true;
            foreach($item->children as $child)
            {
                $hasChildren = $hasChildren && Auth::user()->can('browse', $child);

                if(url($child->link()) == url()->current())
                {
                    array_push($listItemClass, 'active');
                }
            }
            if (!$hasChildren) { continue; }

            $linkAttributes = 'href="#' . str_slug($item->title, '-') .'-dropdown-element" data-toggle="collapse" aria-expanded="'. (in_array('active', $listItemClass) ? 'true' : 'false').'"';
            array_push($listItemClass, 'dropdown');
        }
        else
        {
            $linkAttributes =  'href="' . url($href) .'"';

            if(!Auth::user()->can('browse', $item)) {
                return;
            }
        }
    @endphp

    <li class="{{ implode(" ", $listItemClass) }}">
        <a {!! $linkAttributes !!} target="{{ $item->target }}">
            <span class="icon {{ $item->icon_class }}"></span>
            <span class="title">{{ $item->title }}</span>
        </a>
        @if(!$item->children->isEmpty())
            <div id="{{ str_slug($item->title, '-') }}-dropdown-element" class="panel-collapse collapse {{ (in_array('active', $listItemClass) ? 'in' : '') }}">
                <div class="panel-body">
                    @include('voyager::menu.admin_menu', ['items' => $item->children, 'options' => $options, 'innerLoop' => true])
                </div>
            </div>
        @endif
    </li>
@endforeach

</ul>
