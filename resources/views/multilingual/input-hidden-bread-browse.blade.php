@if (isFieldTranslatable($data, $row))
    <input type="hidden"
           data-multilingual="true"
           name="{{ $row->field.$row->id }}_lg"
           id="{{ $row->field.$row->id }}_lg"
           value="{{ getFieldTranslations($data, $row) }}">
@endif
