<br>
@if(isset($dataTypeContent->{$row->field}))
<?php $images = json_decode($dataTypeContent->{$row->field}); ?>
@if($images != null)
<div class="dd-multiple-images">
    <ol class="dd-multiple-images-list">
        @foreach($images as $image)
        <li class="dd-multiple-images-item" data-field-name="{{ $row->field }}"  data-file-name="{{ $image }}" data-id="{{ $dataTypeContent->id }}" >

            <div class="dd-multiple-images-handle"></div>
            <a href="#" class="voyager-x remove-multi-image" style="position: absolute;"></a>
            <img src="{{ Voyager::image( $image ) }}" data-file-name="{{ $image }}" data-id="{{ $dataTypeContent->id }}" style="max-width:200px; height:auto; clear:both; display:block; padding:2px; border:1px solid #ddd; margin-bottom:5px;">
        </li>
        @endforeach
    </ol>
</div>

@endif
@endif
<div class="clearfix"></div>
<input @if($row->required == 1 && !isset($dataTypeContent->{$row->field})) required @endif type="file" name="{{ $row->field }}[]" multiple="multiple" accept="image/*">
