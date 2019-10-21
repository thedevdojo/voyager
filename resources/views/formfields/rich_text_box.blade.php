<textarea class="form-control richTextBox" name="{{ $row->field }}" id="richtext{{ $row->field }}" {!! outputAriaForHelperText($row) !!}>
    {{ old($row->field, $dataTypeContent->{$row->field} ?? '') }}
</textarea>
