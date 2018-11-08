@if(property_exists($row->details, 'on') && property_exists($row->details, 'off'))
    @if($data->{$row->field})
        <span class="label label-info">{{ $row->details->on }}</span>
    @else
        <span class="label label-primary">{{ $row->details->off }}</span>
    @endif
@else
    {{ $data->{$row->field} }}
@endif
