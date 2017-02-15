<textarea class="form-control richTextBox" name="{{ $row->field }}">
    @if(isset($dataTypeContent->{$row->field}))
        {{ old($row->field, $dataTypeContent->{$row->field}) }}
    @else
        {{old($row->field)}}
    @endif
</textarea>