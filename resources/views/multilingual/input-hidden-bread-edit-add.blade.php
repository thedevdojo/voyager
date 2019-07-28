@if (is_field_translatable($dataTypeContent, $row))
    <span class="language-label js-language-label"></span>
    <input type="hidden"
           data-i18n="true"
           name="{{ $row->field }}_i18n"
           id="{{ $row->field }}_i18n"
           @if(!empty(session()->getOldInput($row->field.'_i18n') && is_null($dataTypeContent->id)))
             value="{{ session()->getOldInput($row->field.'_i18n') }}"
           @else
             value="{{ get_field_translations($dataTypeContent, $row->field) }}"
           @endif>
@endif
