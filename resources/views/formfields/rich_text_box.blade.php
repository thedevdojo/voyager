<textarea class="form-control richTextBox" name="{{ $row->field }}" id="richtext{{ $row->field }}" {!! outputAriaForHelpterText($row) !!}>
    {{ old($row->field, $dataTypeContent->{$row->field} ?? '') }}
</textarea>
