<input type="color" class="form-control" data-name="{{ $row->field }}"  name="{{ $row->field }}"
       value="@if(isset($dataTypeContent->{$row->field})){{ $dataTypeContent->{$row->field} }}@else{{old($row->field)}}@endif">
