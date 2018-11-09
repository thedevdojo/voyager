@if ($action == 'edit' || $action == 'add')
    @section("formfield_edit_add")
        <div id="{{ $row->field }}" data-theme="{{ @$options->theme }}" data-language="{{ @$options->language }}" class="ace_editor min_height_200" name="{{ $row->field }}">@if(isset($dataTypeContent->{$row->field})){{ old($row->field, $dataTypeContent->{$row->field}) }}@elseif(isset($options->default)){{ old($row->field, $options->default) }}@else{{ old($row->field) }}@endif</div>
        <textarea name="{{ $row->field }}" id="{{ $row->field }}_textarea" class="hidden">@if(isset($dataTypeContent->{$row->field})){{ old($row->field, $dataTypeContent->{$row->field}) }}@elseif(isset($options->default)){{ old($row->field, $options->default) }}@else{{ old($row->field) }}@endif</textarea>
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
