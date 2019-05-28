<input type="hidden" class="form-control" name="{{ $row->field }}"
       placeholder="{{ $row->display_name }}"
       {!! isBreadSlugAutoGenerator($options) !!}
       value="{{ $dataTypeContent->{$row->field} ?? old($row->field) ?? $options->default ?? '' }}">
