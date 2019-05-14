<input type="number"
       class="form-control"
       name="{{ $row->field }}"
       type="number"
       @if($row->required == 1) required @endif
       @if(isset($options->min)) {{ 'min='.$options->min }} @endif
       @if(isset($options->max)) {{ 'max='.$options->max }} @endif
       step="{{ $options->step ?? 'any' }}"
       placeholder="{{ isset($options->placeholder)? old($row->field, $options->placeholder): $row->display_name }}"
       value="{{ $dataTypeContent->{$row->field} ?? old($row->field) ?? $options->default ?? '' }}">
