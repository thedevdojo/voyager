@if($data->{$row->field . '_page_slug'})
    <a href="{{ $data->{$row->field . '_page_slug'} }}">{{ $data->{$row->field} }}</a>
@endif