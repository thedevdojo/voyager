<br>
@if(isset($dataTypeContent->{$row->field}))
    <?php $images = json_decode($dataTypeContent->{$row->field}); ?>
    @if($images != null)
        @foreach($images as $image)
            <div class="image-tool-box">
                <img src="{{ Voyager::image( $image ) }}" />
                <div class="image-tools">
                    <i class="glyphicon glyphicon-remove remove-multi-image" title="Remove image"></i>
                </div>
            </div>
        @endforeach
    @endif
@endif
<input type="file" name="{{ $row->field }}[]" multiple="multiple">
