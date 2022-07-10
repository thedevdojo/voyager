<input type="color" class="form-control" name="{{ $row->form_field_name ?? $row->field }}"
       value="{{ old($row->field, $dataTypeContent->{$row->field}) }}">
