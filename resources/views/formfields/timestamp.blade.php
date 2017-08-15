@php
    $details = json_decode($row->details);
    $dateFormat = isset($details->format) ? $details->format : '';
@endphp

<input @if($row->required == 1) required @endif type="datetime" class="form-control datepicker" name="{{ $row->field }}"
       value="@if(isset($dataTypeContent->{$row->field})){{ old($row->field, $dataTypeContent->{$row->field}) }}@else{{old($row->field)}}@endif" data-details="{{ $row->details }}">
