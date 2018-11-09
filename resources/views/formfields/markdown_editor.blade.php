@if ($action == 'browse')

    @include('voyager::multilingual.input-hidden-bread-browse', ['data' => $dataTypeContent])
    <span>{{ $dataTypeContent->{$row->field} }}</span>

@else

    <textarea class="form-control simplemde" name="{{ $row->field }}" id="markdown{{ $row->field }}">@if(isset($dataTypeContent->{$row->field})){{ old($row->field, $dataTypeContent->{$row->field}) }}@else{{ old($row->field) }}@endif</textarea>

@endif
