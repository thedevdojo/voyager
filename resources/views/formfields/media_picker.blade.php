<div class="panel">
    <div class="page-content settings container-fluid">
        <div id="media_picker_{{ $row->field }}">
            <media-manager
                base-path="{{ $options->base_path ?? '/'.$dataType->slug.'/' }}"
                filename="{{ $options->rename ?? 'null' }}"
                :allow-multi-select="{{ isset($options->max) && $options->max > 1 ? 'true' : 'false' }}"
                :max-selected-files="{{ $options->max ?? 0 }}"
                :min-selected-files="{{ $options->min ?? 0 }}"
                :show-folders="{{ var_export($options->show_folders ?? false, true) }}"
                :show-toolbar="{{ var_export($options->show_toolbar ?? true, true) }}"
                :allow-upload="{{ var_export($options->allow_upload ?? true, true) }}"
                :allow-move="{{ var_export($options->allow_move ?? false, true) }}"
                :allow-delete="{{ var_export($options->allow_delete ?? true, true) }}"
                :allow-create-folder="{{ var_export($options->allow_create_folder ?? true, true) }}"
                :allow-rename="{{ var_export($options->allow_rename ?? true, true) }}"
                :allow-crop="{{ var_export($options->allow_crop ?? true, true) }}"
                :allowed-types="{{ isset($options->allowed) && is_array($options->allowed) ? json_encode($options->allowed) : '[]' }}"
                :pre-select="false"
                :expanded="{{ var_export($options->expanded ?? false, true) }}"
                :show-expand-button="true"
                :element="'input[name=&quot;{{ $row->field }}&quot;]'"
                :details="{{ json_encode($options ?? []) }}"
            ></media-manager>
			@php
				$path = substr($content, 1, strlen($content)-1);
				$path = substr($path, 0, strlen($path)-1);
				$path = Storage::disk(config('voyager.storage.disk'))->path('').$path;
			@endphp
			@if (file_exists($path))
				<input type="hidden" :value="{{ $content }}" name="{{ $row->field }}">
			@else
				<input type="hidden" :value="''" name="{{ $row->field }}">
			@endif
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
