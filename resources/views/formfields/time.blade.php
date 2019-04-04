<input @if($row->required == 1) required @endif type="time"  data-name="{{ $row->getTranslatedAttribute('display_name') }}"  class="form-control" name="{{ $row->field }}"
       placeholder="{{ isset($options->placeholder)? old($row->field, $options->placeholder): $row->getTranslatedAttribute('display_name') }}"
       {!! isBreadSlugAutoGenerator($options) !!}
       value="{{ $dataTypeContent->{$row->field} ?? old($row->field) ?? $options->default ?? '' }}">
