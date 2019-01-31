@foreach($dataType->browseRows as $row)
<th>
    @if ($isServerSide)
        <a href="{{ $row->sortByUrl($orderBy, $sortOrder) }}">
    @endif
    {{ $row->display_name }}
    @if ($isServerSide)
        @if ($row->isCurrentSortField($orderBy))
            @if ($sortOrder == 'asc')
                <i class="voyager-angle-up pull-right"></i>
            @else
                <i class="voyager-angle-down pull-right"></i>
            @endif
        @endif
        </a>
    @endif
</th>
@endforeach