@if ($action == 'edit' || $action == 'add')
    @section("formfield_edit_add")
        <input type="number"
               class="form-control"
               name="{{ $row->field }}"
               type="number"
               @if($row->required == 1) required @endif
               step="any"
               placeholder="{{ isset($options->placeholder)? old($row->field, $options->placeholder): $row->display_name }}"
               value="@if(isset($dataTypeContent->{$row->field})){{ old($row->field, $dataTypeContent->{$row->field}) }}@else{{old($row->field)}}@endif">
    @overwrite
@endif

{{--  Render BREA[D] --}}

@if ($action == 'browse')
    @section("formfield_browse")

        @include('voyager::multilingual.input-hidden-bread-browse', ['data' => $dataTypeContent])
        <span>{{ $dataTypeContent->{$row->field} }}</span>

    @overwrite
@endif

@if ($action == 'read')
    @section("formfield_read")
        @include('voyager::multilingual.input-hidden-bread-read')
        <p>{{ $dataTypeContent->{$row->field} }}</p>
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