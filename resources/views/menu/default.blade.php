<ul>

@foreach ($items->sortBy('order') as $item)
    
    @php

        $isActive = null;
        $styles = null;
        $icon = null;

        // Background Color or Color
        if (isset($options->color) && $options->color == true) {
            $styles = 'color:'.$item->color;
        }
        if (isset($options->background) && $options->background == true) {
            $styles = 'background-color:'.$item->color;
        }

        // Check if link is current
        if(url($item->url) == url()->current()){
            $isActive = 'active';
        }

        // Set Icon
        if(isset($options->icon) && $options->icon == true){
            $icon = '<i class="' . $item->icon_class . '"></i>';
        }
        
    @endphp

    <li class="{{ $isActive }}">
        <a href="{{ url($item->url) }}" target="{{ $item->target }}" style="{{ $styles }}">
            {!! $icon !!}
            <span>{{ $item->title }}</span>
        </a>
        @if(!$item->children->isEmpty())
        @include('voyager::menu.default', ['items' => $item->children, 'options' => $options])
        @endif
    </li>
@endforeach

</ul>