@if ($action == 'edit' || $action == 'add')
    @section("formfield_edit_add")
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
    @overwrite
@endif

{{--  Render BREA[D] --}}

@if ($action == 'browse')
    @section("formfield_browse")
        @if(property_exists($row->details, 'on') && property_exists($row->details, 'off'))
            @if($dataTypeContent->{$row->field})
                <span class="label label-info">{{ $row->details->on }}</span>
            @else
                <span class="label label-primary">{{ $row->details->off }}</span>
            @endif
        @else
            {{ $dataTypeContent->{$row->field} }}
        @endif
    @overwrite
@endif

@if ($action == 'read')
    @section("formfield_read")
        @if(property_exists($row->details, 'on') && property_exists($row->details, 'off'))
            @if($dataTypeContent->{$row->field})
                <span class="label label-info">{{ $row->details->on }}</span>
            @else
                <span class="label label-primary">{{ $row->details->off }}</span>
            @endif
        @else
            {{ $dataTypeContent->{$row->field} }}
        @endif
    @overwrite
@endif

@if ($action == 'edit')
    @section("formfield_edit")
        @yield("formfield_edit_add")
    @overwrite
@endif

@if ($action == 'add')
    @section("formfield_add")
        @yield("formfield_edit_add")
    @overwrite
@endif

@yield("formfield_{$action}")

