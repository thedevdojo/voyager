@php
    $details = json_decode($row->details);
    $datetimepicker = isset($details->datetimepicker) ? json_encode($details->datetimepicker) : false;
@endphp

@if($datetimepicker)
    <input @if($row->required == 1) required @endif type="datetime" class="form-control datepicker" name="{{ $row->field }}"
           value="@if(isset($dataTypeContent->{$row->field})){{ old($row->field, $dataTypeContent->{$row->field}) }}@else{{old($row->field)}}@endif" data-datetimepicker="{{ $datetimepicker }}">
@else
    <input @if($row->required == 1) required @endif type="datetime" class="form-control datepicker" name="{{ $row->field }}"
           value="@if(isset($dataTypeContent->{$row->field})){{ gmdate('m/d/Y g:i A', strtotime(old($row->field, $dataTypeContent->{$row->field})))  }}@else{{old($row->field)}}@endif">
@endif
