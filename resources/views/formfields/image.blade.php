@if(isset($dataTypeContent->{$row->field}))
    <img src="{{ Voyager::image($dataTypeContent->{$row->field}) }}"
         style="width:200px; height:auto; clear:both; display:block; padding:2px; border:1px solid #ddd; margin-bottom:10px;">
@endif
<input @if($row->required == 1) required @endif type="file" name="{{ $row->field }}">