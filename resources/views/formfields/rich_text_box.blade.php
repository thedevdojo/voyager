<textarea @if($row->required == 1) required @endif class="form-control richTextBox" data-name="{{ $row->display_name }}"  name="{{ $row->field }}" id="richtext{{ $row->field }}">
    @if(isset($dataTypeContent->{$row->field}))
        {{ old($row->field, $dataTypeContent->{$row->field}) }}
    @else
        {{old($row->field)}}
    @endif
</textarea>
