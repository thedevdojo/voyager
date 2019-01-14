@php
$content = '';
if (isset($row->details->max) && $row->details->max == 1) {
    $content = "'".$dataTypeContent->{$row->field}."'";
} else {
    json_decode($dataTypeContent->{$row->field});
    if (json_last_error() == JSON_ERROR_NONE) {
        $content = json_encode($dataTypeContent->{$row->field});
    } else {
        $content = json_encode('[]');
    }
}
@endphp

<div id="media_picker_{{ $row->field }}">
    <media-manager
        base-path="{{ isset($row->details->base_path) ? $row->details->base_path : '/' }}"
        :allow-multi-select="{{ isset($row->details->max) && $row->details->max > 1 ? 'true' : 'false' }}"
        :max-selected-files="{{ isset($row->details->max) ? $row->details->max : 0 }}"
        :min-selected-files="{{ isset($row->details->min) ? $row->details->min : 0 }}"
        :show-folders="{{ isset($row->details->show_folders) ? $row->details->show_folders : 'false' }}"
        :show-toolbar="{{ isset($row->details->show_toolbar) ? $row->details->show_toolbar : 'true' }}"
        :allow-upload="{{ isset($row->details->allow_upload) ? $row->details->allow_upload : 'true' }}"
        :allow-move="{{ isset($row->details->allow_move) ? $row->details->allow_move : 'false' }}"
        :allow-delete="{{ isset($row->details->allow_delete) ? $row->details->allow_delete : 'true' }}"
        :allow-create-folder="{{ isset($row->details->allow_create_folder) ? $row->details->allow_create_folder : 'true' }}"
        :allow-rename="{{ isset($row->details->allow_rename) ? $row->details->allow_rename : 'true' }}"
        :allow-crop="{{ isset($row->details->allow_crop) ? $row->details->allow_crop : 'true' }}"
        :allowed-types="{{ isset($row->details->allowed) && is_array($row->details->allowed) ? json_encode($row->details->allowed) : '[]' }}"
        :pre-select="false"
        :expanded="false"
        :show-expand-button="true"
        :element="'input[name=&quot;{{ $row->field }}&quot;]'"
    ></media-manager>
    <input type="hidden" :value="{{ $content }}" name="{{ $row->field }}">
</div>
@push('javascript')
<script>
new Vue({
    el: '#media_picker_{{ $row->field }}'
});
</script>
@endpush
