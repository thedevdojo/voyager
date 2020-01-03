<li class="dd-item" data-id="{{ $result->id }}">

    <button class="dd-collapse" data-action="collapse" type="button">Collapse</button>
    <button class="dd-expand" data-action="expand" type="button">Expand</button>

    @if (isset($child_view_render)) {!! $child_view_render !!} @endif

    <ol class="dd-list">

        @if (isset($childrens))
            {!! $childrens !!}
        @endif
        
    </ol>

</li>
