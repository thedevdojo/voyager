<input type="color" class="form-control" name="{{ $row->field }}"
       {!! outputAriaForHelpterText($row) !!}
       value="{{ old($row->field, $dataTypeContent->{$row->field}) }}">
