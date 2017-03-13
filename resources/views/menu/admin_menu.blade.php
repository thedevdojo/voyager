<ul class="nav navbar-nav">

@php
    if (Voyager::translatable($items)) {
        $items = $items->load('translations');
    }

@endphp

@foreach ($items->sortBy('order') as $item)
    
    @php
        $originalItem = $item;
        if (Voyager::translatable($item)) {
            $item = $item->translate($options->locale);
        }

        // TODO - still a bit ugly - can move some of this stuff off to a helper in the future.
        $listItemClass = [];
        $styles = null;
        $linkAttributes = null;

        if(url($item->link()) == url()->current())
        {
            array_push($listItemClass,'active');
        }

        // With Children Attributes
        if(!$originalItem->children->isEmpty())
        {
            foreach($originalItem->children as $children)
            {
                if(url($children->link()) == url()->current())
                {
                    array_push($listItemClass,'active');
                }
            }
            $linkAttributes =  'href="#' . str_slug($item->title, '-') .'-dropdown-element" data-toggle="collapse" aria-expanded="'. (in_array('active', $listItemClass) ? 'true' : 'false').'"';
            array_push($listItemClass, 'dropdown');
        }
        else
        {
            $linkAttributes =  'href="' . url($item->link()) .'"';
        }

        // Permission Checker
        $self_prefix = str_replace('/', '\/', $options->user->prefix);
        $slug = str_replace('/', '', preg_replace('/^\/'.$self_prefix.'/', '', $item->link()));

        if ($slug != '') {
            // Get dataType using slug
            $dataType = $options->user->dataTypes->first(function ($value) use ($slug) {
                return $value->slug == $slug;
            });

            if ($dataType) {
                // Check if datatype permission exist
                $exist = $options->user->permissions->first(function ($value) use ($dataType) {
                    return $value->key == 'browse_'.$dataType->name;
                });
            } else {
                // Check if admin permission exists
                $exist = $options->user->permissions->first(function ($value) use ($slug) {
                    return $value->key == 'browse_'.$slug && is_null($value->table_name);
                });
            }

            if ($exist) {
                // Check if current user has access
                if (!in_array($exist->key, $options->user->user_permissions)) {
                    continue;
                }
            }
        }
        
    @endphp

    <li class="{{ implode(" ", $listItemClass) }}">
        <a {!! $linkAttributes !!} target="{{ $item->target }}">
            <span class="icon {{ $item->icon_class }}"></span>
            <span class="title">{{ $item->title }}</span>
        </a>
        @if(!$originalItem->children->isEmpty())
        <div id="{{ str_slug($originalItem->title, '-') }}-dropdown-element" class="panel-collapse collapse {{ (in_array('active', $listItemClass) ? 'in' : '') }}">
            <div class="panel-body">
                @include('voyager::menu.admin_menu', ['items' => $originalItem->children, 'options' => $options, 'innerLoop' => true])
            </div>
        </div>
        @endif
    </li>
@endforeach

</ul>
