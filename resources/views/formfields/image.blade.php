@if ($action == 'edit' || $action == 'add')
    @section("formfield_edit_add")
        @if(isset($dataTypeContent->{$row->field}))
            <img src="@if( !filter_var($dataTypeContent->{$row->field}, FILTER_VALIDATE_URL)){{ Voyager::image( $dataTypeContent->{$row->field} ) }}@else{{ $dataTypeContent->{$row->field} }}@endif"
                 style="max-width:200px; height:auto; clear:both; display:block; padding:2px; border:1px solid #ddd; margin-bottom:10px;">
        @endif
        <input @if($row->required == 1 && !isset($dataTypeContent->{$row->field})) required @endif type="file" name="{{ $row->field }}" accept="image/*">
    @overwrite
@endif

{{--  Render BREA[D] --}}

@if ($action == 'browse')
    @section("formfield_browse")
        <img src="@if( !filter_var($dataTypeContent->{$row->field}, FILTER_VALIDATE_URL)){{ Voyager::image( $dataTypeContent->{$row->field} ) }}@else{{ $dataTypeContent->{$row->field} }}@endif" style="width:100px">
    @overwrite
@endif

@if ($action == 'read')
    @section("formfield_read")
        <img class="img-responsive"
             src="{{ filter_var($dataTypeContent->{$row->field}, FILTER_VALIDATE_URL) ? $dataTypeContent->{$row->field} : Voyager::image($dataTypeContent->{$row->field}) }}">
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