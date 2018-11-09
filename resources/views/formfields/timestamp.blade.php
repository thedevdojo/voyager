@if ($action == 'edit' || $action == 'add')
    @section("formfield_edit_add")
        <input @if($row->required == 1) required @endif type="datetime" class="form-control datepicker" name="{{ $row->field }}"
               value="@if(isset($dataTypeContent->{$row->field})){{ \Carbon\Carbon::parse(old($row->field, $dataTypeContent->{$row->field}))->format('m/d/Y g:i A') }}@else{{old($row->field)}}@endif">
    @overwrite
@endif

{{--  Render BREA[D] --}}

@if ($action == 'browse')
    @section("formfield_browse")
        {{ property_exists($row->details, 'format') ? \Carbon\Carbon::parse($dataTypeContent->{$row->field})->formatLocalized($row->details->format) : $dataTypeContent->{$row->field} }}
    @overwrite
@endif

@if ($action == 'read')
    @section("formfield_read")
        {{ property_exists($row->details, 'format') ? \Carbon\Carbon::parse($dataTypeContent->{$row->field})->formatLocalized($row->details->format) : $dataTypeContent->{$row->field} }}
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