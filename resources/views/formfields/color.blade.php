<input type="color" class="form-control" name="{{ $row->field }}"
       value="{{ $dataTypeContent->{$row->field} ?? old($row->field) }}">
