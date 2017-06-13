@if(isset($dataTypeContent->{$row->field}))
    <a class="fileType" href="/storage/{{ $dataTypeContent->{$row->field} }}">Download</a>
@endif
<input @if($row->required == 1) required @endif type="file" name="{{ $row->field }}">