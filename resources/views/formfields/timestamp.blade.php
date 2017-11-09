<input @if($row->required == 1) required @endif type="datetime" class="form-control datepicker"  data-name="{{ $row->display_name }}"  name="{{ $row->field }}"
       value="@if(isset($dataTypeContent->{$row->field})){{ gmdate('m/d/Y g:i A', strtotime(old($row->field, $dataTypeContent->{$row->field})))  }}@else{{old($row->field)}}@endif">
