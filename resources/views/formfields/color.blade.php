@if ($action == 'edit' || $action == 'add')
    @section("formfield_edit_add")
        <input type="color" class="form-control" name="{{ $row->field }}"
               value="@if(isset($dataTypeContent->{$row->field})){{ $dataTypeContent->{$row->field} }}@else{{old($row->field)}}@endif">
    @overwrite
@endif

{{--  Render BREA[D] --}}

@if ($action == 'browse')
    @section("formfield_browse")
        <span class="badge badge-lg" style="background-color: {{ $data->{$row->field} }}">{{ $dataTypeContent->{$row->field} }}</span>
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

