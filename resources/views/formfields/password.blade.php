@if ($action == 'edit' || $action == 'add')
    @section("formfield_edit_add")
        @if(isset($dataTypeContent->{$row->field}))
            <br>
            <small>{{ __('voyager::form.field_password_keep') }}</small>
        @endif
        <input type="password"
               @if($row->required == 1 && !isset($dataTypeContent->{$row->field})) required @endif
               class="form-control"
               name="{{ $row->field }}"
               value="">
    @overwrite
@endif

{{--  Render BREA[D] --}}

@if ($action == 'browse')
    @section("formfield_browse")
    @overwrite
@endif

@if ($action == 'read')
    @section("formfield_read")
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