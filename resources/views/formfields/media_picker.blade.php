<div class="panel">
    <div class="page-content settings container-fluid">
        <div id="media_picker_{{ $row->field }}">
            <media-manager
                base-path="{{ isset($options->base_path) ? $options->base_path : '/'.$dataType->slug.'/' }}"
                filename="{{ isset($options->rename) ? $options->rename : 'null' }}"
                :allow-multi-select="{{ isset($options->max) && $options->max > 1 ? 'true' : 'false' }}"
                :max-selected-files="{{ isset($options->max) ? $options->max : 0 }}"
                :min-selected-files="{{ isset($options->min) ? $options->min : 0 }}"
                :show-folders="{{ (isset($options->show_folders) ? $options->show_folders : false) ? 'true' : 'false' }}"
                :show-toolbar="{{ (isset($options->show_toolbar) ? $options->show_toolbar : true) ? 'true' : 'false' }}"
                :allow-upload="{{ (isset($options->allow_upload) ? $options->allow_upload : true) ? 'true' : 'false' }}"
                :allow-move="{{ (isset($options->allow_move) ? $options->allow_move : false) ? 'true' : 'false' }}"
                :allow-delete="{{ (isset($options->allow_delete) ? $options->allow_delete : true) ? 'true' : 'false' }}"
                :allow-create-folder="{{ (isset($options->allow_create_folder) ? $options->allow_create_folder : true) ? 'true' : 'false' }}"
                :allow-rename="{{ (isset($options->allow_rename) ? $options->allow_rename : true) ? 'true' : 'false' }}"
                :allow-crop="{{ (isset($options->allow_crop) ? $options->allow_crop : true) ? 'true' : 'false' }}"
                :allowed-types="{{ isset($options->allowed) && is_array($options->allowed) ? json_encode($options->allowed) : '[]' }}"
                :pre-select="false"
                :expanded="false"
                :show-expand-button="true"
                :element="'input[name=&quot;{{ $row->field }}&quot;]'"
            ></media-manager>
            <input type="hidden" :value="{{ $content }}" name="{{ $row->field }}">
        </div>
    </div>
</div>
@push('javascript')
<script>
new Vue({
    el: '#media_picker_{{ $row->field }}'
});
</script>
@endpush
