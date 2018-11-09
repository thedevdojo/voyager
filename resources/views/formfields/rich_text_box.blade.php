@if ($action == 'edit' || $action == 'add')
    @section("formfield_edit_add")
        <textarea @if($row->required == 1) required @endif class="form-control richTextBox" name="{{ $row->field }}" id="richtext{{ $row->field }}">
        @if(isset($dataTypeContent->{$row->field}))
                {{ old($row->field, $dataTypeContent->{$row->field}) }}
            @else
                {{old($row->field)}}
            @endif
        </textarea>
    @overwrite
@endif

{{--  Render BREA[D] --}}

@if ($action == 'browse')
    @section("formfield_browse")
        @include('voyager::multilingual.input-hidden-bread-browse', ['data' => $dataTypeContent])
        <div class="readmore">{{ mb_strlen( strip_tags($dataTypeContent->{$row->field}, '<b><i><u>') ) > 200 ? mb_substr(strip_tags($dataTypeContent->{$row->field}, '<b><i><u>'), 0, 200) . ' ...' : strip_tags($dataTypeContent->{$row->field}, '<b><i><u>') }}</div>

    @overwrite
@endif

@if ($action == 'read')
    @section("formfield_read")
        @include('voyager::multilingual.input-hidden-bread-read')
        <p>{!! $dataTypeContent->{$row->field} !!}</p>
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