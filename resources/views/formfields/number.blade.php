<input type="number"
       class="form-control"
       name="{{ $row->field }}"
       type="number"
       @if($row->required == 1) required @endif
       step="any"
       placeholder="{{ old($row->field, $options->placeholder ?? $row->display_name) }}"
       value="{{ old($row->field, $dataTypeContent->{$row->field} ?? $options->default ?? '') }}">
