<input type="hidden" class="form-control" name="{{ $row->field }}"
       placeholder="{{ $row->getTranslatedAttribute('display_name') }}"
       {!! isBreadSlugAutoGenerator($options) !!}
       value="{{ old($row->field, $dataTypeContent->{$row->field} ?? $options->default ?? '') }}">
