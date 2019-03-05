<input type="number"
       class="form-control"
       name="{{ $row->field }}"
       type="number"
       @if($row->required == 1) required @endif
       step="any"
       placeholder="{{ isset($options->placeholder)? old($row->field, $options->placeholder): $row->display_name }}"
       value="{{ $dataTypeContent->{$row->field} ?? old($row->field) ?? $options->default ?? '' }}">
