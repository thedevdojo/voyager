<input type="date" class="form-control" name="{{ $row->field }}"
       placeholder="{{ $row->display_name }}"
       data-datetimepicker-options="@if(isset($options->datetimepicker)){{ json_encode($options->datetimepicker) }}@endif"
       value="@if(isset($dataTypeContent->{$row->field})){{ \Carbon\Carbon::parse(old($row->field, $dataTypeContent->{$row->field}))->format('Y-m-d') }}@else{{old($row->field)}}@endif">
