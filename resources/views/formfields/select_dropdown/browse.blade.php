
        @if($dataTypeContent->{$row->field . '_page_slug'})
            <a href="{{ $dataTypeContent->{$row->field . '_page_slug'} }}">{{ $dataTypeContent->{$row->field} }}</a>
        @endif
