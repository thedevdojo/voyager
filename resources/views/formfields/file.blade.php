@if(isset($dataTypeContent->{$row->field}))
    <a class="fileType" href="/storage/{{ $dataTypeContent->{$row->field} }}">Download</a>
@endif
<input type="file" name="{{ $row->field }}">