@if(isset($dataTypeContent->{$row->field}))
    <?php $pics = json_decode($dataTypeContent->{$row->field}); ?>
    @if($pics != null)
        @foreach($pics as $pic)
            <img src="{{ Voyager::image( $pic ) }}" style="width:200px; height:auto; clear:both; padding:2px; border:1px solid #ddd; margin-bottom:10px;" />
        @endforeach
    @endif
@endif
<input type="file" name="{{ $row->field }}[]" multiple="multiple">
