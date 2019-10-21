<input @if($row->required == 1) required @endif type="datetime" class="form-control datepicker" name="{{ $row->field }}"
       value="@if(isset($dataTypeContent->{$row->field})){{ \Carbon\Carbon::parse(old($row->field, $dataTypeContent->{$row->field}))->format('Y-m-d h:i') }}@else{{old($row->field)}}@endif">
