<br>
@if(isset($dataTypeContent->{$row->field}))
    <?php $images = json_decode($dataTypeContent->{$row->field}); ?>
    @if($images != null)
    <div class="dd">
      <ol class="dd-list">
        @foreach($images as $image)
            <li   class="img_settings_container dd-item" data-id="{{ $dataTypeContent->id }}"  data-image="{{ $image }}" data-field-name="{{ $row->field }}">
                <img class="dd-handle" src="{{ Voyager::image( $image ) }}" data-image="{{ $image }}" data-id="{{ $dataTypeContent->id }}" style="max-width:100px; height:auto; clear:both; display:block; padding:2px; border:1px solid #ddd; margin-bottom:5px;">
                <a href="#" class="voyager-x remove-multi-image"></a>
            </li>
        @endforeach
      </ol>
    </div>
    @endif
@endif
<div class="clearfix"></div>
<input @if($row->required == 1) required @endif type="file" name="{{ $row->field }}[]" multiple="multiple" accept="image/*">
