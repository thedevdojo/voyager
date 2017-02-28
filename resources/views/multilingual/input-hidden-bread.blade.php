@if (isFieldTranslatable($dataTypeContent, $row))
    <input type="hidden"
           data-i18n="true"
           name="{{ $row->field }}_lg"
           id="{{ $row->field }}_lg"
           value="{{ getFieldTranslations($dataTypeContent, $row) }}">
@endif
