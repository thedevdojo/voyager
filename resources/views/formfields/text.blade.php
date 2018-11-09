@if ($action == 'edit' || $action == 'add')
    @section("formfield_edit_add")
        <input @if($row->required == 1) required @endif type="text" class="form-control" name="{{ $row->field }}"
               placeholder="{{ isset($options->placeholder)? old($row->field, $options->placeholder): $row->display_name }}"
               {!! isBreadSlugAutoGenerator($options) !!}
               value="@if(isset($dataTypeContent->{$row->field})){{ old($row->field, $dataTypeContent->{$row->field}) }}@elseif(isset($options->default)){{ old($row->field, $options->default) }}@else{{ old($row->field) }}@endif">
    @overwrite
@endif

{{--  Render BREA[D] --}}

@if ($action == 'browse')
    @section("formfield_browse")
        @include('voyager::multilingual.input-hidden-bread-browse', ['data' => $dataTypeContent])
        <div class="readmore">{{ mb_strlen( $dataTypeContent->{$row->field} ) > 200 ? mb_substr($dataTypeContent->{$row->field}, 0, 200) . ' ...' : $dataTypeContent->{$row->field} }}</div>
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