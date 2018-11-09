@if ($action == 'edit' || $action == 'add')
    @section("formfield_edit_add")
        <br>
        @if(isset($dataTypeContent->{$row->field}))
            <?php $images = json_decode($dataTypeContent->{$row->field}); ?>
            @if($images != null)
                @foreach($images as $image)
                    <div class="img_settings_container" data-field-name="{{ $row->field }}" style="float:left;padding-right:15px;">
                        <img src="{{ Voyager::image( $image ) }}" data-image="{{ $image }}" data-id="{{ $dataTypeContent->getKey() }}" style="max-width:200px; height:auto; clear:both; display:block; padding:2px; border:1px solid #ddd; margin-bottom:5px;">
                        <a href="#" class="voyager-x remove-multi-image"></a>
                    </div>
                @endforeach
            @endif
        @endif
        <div class="clearfix"></div>
        <input @if($row->required == 1) required @endif type="file" name="{{ $row->field }}[]" multiple="multiple" accept="image/*">
    @overwrite
@endif

{{--  Render BREA[D] --}}

@if ($action == 'browse')
    @section("formfield_browse")
        @php $images = json_decode($dataTypeContent->{$row->field}); @endphp
        @if($images)
            @php $images = array_slice($images, 0, 3); @endphp
            @foreach($images as $image)
                <img src="@if( !filter_var($image, FILTER_VALIDATE_URL)){{ Voyager::image( $image ) }}@else{{ $image }}@endif" style="width:50px">
            @endforeach
        @endif
    @overwrite
@endif

@if ($action == 'read')
    @section("formfield_read")
        @if(json_decode($dataTypeContent->{$row->field}))
            @foreach(json_decode($dataTypeContent->{$row->field}) as $file)
                <img class="img-responsive"
                     src="{{ filter_var($file, FILTER_VALIDATE_URL) ? $file : Voyager::image($file) }}">
            @endforeach
        @else
            <img class="img-responsive"
                 src="{{ filter_var($dataTypeContent->{$row->field}, FILTER_VALIDATE_URL) ? $dataTypeContent->{$row->field} : Voyager::image($dataTypeContent->{$row->field}) }}">
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