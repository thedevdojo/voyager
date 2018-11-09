
        @if(property_exists($row->details, 'on') && property_exists($row->details, 'off'))
            @if($dataTypeContent->{$row->field})
                <span class="label label-info">{{ $row->details->on }}</span>
            @else
                <span class="label label-primary">{{ $row->details->off }}</span>
            @endif
        @else
            {{ $dataTypeContent->{$row->field} }}
        @endif
