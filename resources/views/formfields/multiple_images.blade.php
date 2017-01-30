@if(isset($dataTypeContent->{$row->field}))
    <?php $images = json_decode($dataTypeContent->{$row->field}); ?>
    @if($images != null)
        @foreach($images as $image)
            <img src="{{ Voyager::image( $image ) }}" style="width:200px; height:auto; clear:both; padding:2px; border:1px solid #ddd; margin-bottom:10px;" />
        @endforeach
    @endif
@endif
<input type="file" name="{{ $row->field }}[]" multiple="multiple">
