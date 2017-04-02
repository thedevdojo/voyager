@if (isFieldTranslatable($data, $row))
    <input type="hidden"
           data-i18n="true"
           name="{{ $row->field.$row->id }}_i18n"
           id="{{ $row->field.$row->id }}_i18n"
           value="{{ getFieldTranslations($data, $row->field) }}">
@endif
