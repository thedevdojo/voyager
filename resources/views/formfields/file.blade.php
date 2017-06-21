@if(isset($dataTypeContent->{$row->field}))
    <a class="fileType" href="/storage/{{ $dataTypeContent->{$row->field} }}">{{ {{ __('voyager.generic.download') }} }}</a>
@endif
<input type="file" name="{{ $row->field }}">