@if (isFieldTranslatable($dataTypeContent, $row))
    <input type="hidden"
           data-i18n="true"
           name="{{ $row->field }}_i18n"
           id="{{ $row->field }}_i18n"
           value="{{ getFieldTranslations($dataTypeContent, $row->field) }}">
@endif
