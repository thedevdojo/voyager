
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
                    {{ __('voyager::generic.download') }}
                </a>
            @endif
        @endif
