@if(isset($dataTypeContent->{$row->field}))
    @if(json_decode($dataTypeContent->{$row->field}))
        @foreach(json_decode($dataTypeContent->{$row->field}) as $file)
            <br/><a class="fileType" target="_blank" href="{{ Storage::disk(config('voyager.storage.disk'))->url($file->download_link) or '' }}"> {{ Storage::disk(config('voyager.storage.disk'))->url($file->original_name) or '' }} </a>
        @endforeach
    @else
        <a class="fileType" target="_blank" href="{{ Storage::disk(config('voyager.storage.disk'))->url($dataTypeContent->{$row->field}) }}"> Download </a>
    @endif
@endif
<input @if($row->required == 1) @endif type="file" name="{{ $row->field }}[]" multiple="multiple">
