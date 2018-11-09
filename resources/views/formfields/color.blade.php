@if ($action == 'browse')

    <span class="badge badge-lg" style="background-color: {{ $data->{$row->field} }}">{{ $dataTypeContent->{$row->field} }}</span>

@else

    <input type="color" class="form-control" name="{{ $row->field }}"
           value="@if(isset($dataTypeContent->{$row->field})){{ $dataTypeContent->{$row->field} }}@else{{old($row->field)}}@endif">

@endif
