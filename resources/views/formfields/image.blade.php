<br>
@if(isset($dataTypeContent->{$row->field}))
    <div class="img_settings_container" data-field-name="{{ $row->field }}">
        <img src="{{ Voyager::image($dataTypeContent->{$row->field}) }}" data-image="{{ $dataTypeContent->{$row->field} }}" data-id="{{ $dataTypeContent->id }}">
        <a href="#" class="voyager-x remove-multi-image"></a>
    </div>
@endif
<input @if($row->required == 1 && !isset($dataTypeContent->{$row->field})) required @endif type="file" data-name="{{ $row->display_name }}"  name="{{ $row->field }}">
