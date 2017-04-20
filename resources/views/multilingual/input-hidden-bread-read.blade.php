@if (is_field_translatable($dataTypeContent, $row))
    <input type="hidden"
           data-i18n="true"
           name="{{ $row->field }}_i18n"
           id="{{ $row->field }}_i18n"
           value="{{ get_field_translations($dataTypeContent, $row->field, $row->type, true) }}">
@endif
