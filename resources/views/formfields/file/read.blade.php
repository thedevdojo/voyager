
        @if(json_decode($dataTypeContent->{$row->field}))
            @foreach(json_decode($dataTypeContent->{$row->field}) as $file)
                <a href="{{ Storage::disk(config('voyager.storage.disk'))->url($file->download_link) ?: '' }}">
                    {{ $file->original_name ?: '' }}
                </a>
                <br/>
            @endforeach
        @else
            <a href="{{ Storage::disk(config('voyager.storage.disk'))->url($row->field) ?: '' }}">
                {{ __('voyager::generic.download') }}
            </a>
        @endif
