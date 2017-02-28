@if (isFieldTranslatable($dataTypeContent, $row))
    <input type="hidden"
           data-multilingual="true"
           name="{{ $row->field }}_lg"
           id="{{ $row->field }}_lg"
           value="{{ getFieldTranslations($dataTypeContent, $row) }}">
@endif
