@if(isset($dataTypeContent->{$row->field}))
    <div class="fileType">{{ $dataTypeContent->{$row->field} }}</div>
@endif
<input type="file" name="{{ $row->field }}">