@if ($action == 'browse')

    @if(property_exists($row->details, 'on') && property_exists($row->details, 'off'))
        @if($dataTypeContent->{$row->field})
            <span class="label label-info">{{ $row->details->on }}</span>
        @else
            <span class="label label-primary">{{ $row->details->off }}</span>
        @endif
    @else
        {{ $dataTypeContent->{$row->field} }}
    @endif

@else

    <br>
    <?php $checked = false; ?>
    @if(isset($dataTypeContent->{$row->field}) || old($row->field))
        <?php $checked = old($row->field, $dataTypeContent->{$row->field}); ?>
    @else
        <?php $checked = isset($options->checked) && $options->checked ? true : false; ?>
    @endif

    @if(isset($options->on) && isset($options->off))
        <input type="checkbox" name="{{ $row->field }}" class="toggleswitch"
               data-on="{{ $options->on }}" {!! $checked ? 'checked="checked"' : '' !!}
               data-off="{{ $options->off }}">
    @else
        <input type="checkbox" name="{{ $row->field }}" class="toggleswitch"
               @if($checked) checked @endif>
    @endif

@endif
