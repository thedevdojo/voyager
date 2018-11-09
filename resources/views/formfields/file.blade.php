@if ($action == 'browse')

    @if (!empty($dataTypeContent->{$row->field}) )
        @include('voyager::multilingual.input-hidden-bread-browse', ['data' => $dataTypeContent])
        @if(json_decode($dataTypeContent->{$row->field}))
            @foreach(json_decode($dataTypeContent->{$row->field}) as $file)
                <a href="{{ Storage::disk(config('voyager.storage.disk'))->url($file->download_link) ?: '' }}" target="_blank">
                    {{ $file->original_name ?: '' }}
                </a>
                <br/>
            @endforeach
        @else
            <a href="{{ Storage::disk(config('voyager.storage.disk'))->url($dataTypeContent->{$row->field}) }}" target="_blank">
                Download
            </a>
        @endif
    @endif

@else

    @if(isset($dataTypeContent->{$row->field}))
        @if(json_decode($dataTypeContent->{$row->field}))
            @foreach(json_decode($dataTypeContent->{$row->field}) as $file)
                <br/><a class="fileType" target="_blank" href="{{ Storage::disk(config('voyager.storage.disk'))->url($file->download_link) ?: '' }}"> {{ $file->original_name ?: '' }} </a>
            @endforeach
        @else
            <a class="fileType" target="_blank" href="{{ Storage::disk(config('voyager.storage.disk'))->url($dataTypeContent->{$row->field}) }}"> Download </a>
        @endif
    @endif
    <input @if($row->required == 1 && !isset($dataTypeContent->{$row->field})) required @endif type="file" name="{{ $row->field }}[]" multiple="multiple">

@endif
