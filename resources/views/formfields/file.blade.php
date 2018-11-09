@if ($action == 'edit' || $action == 'add')
    @section("formfield_edit_add")
        @if(isset($dataTypeContent->{$row->field}))
            @if(json_decode($dataTypeContent->{$row->field}))
                @foreach(json_decode($dataTypeContent->{$row->field}) as $file)
                    <br/><a class="fileType" target="_blank" href="{{ Storage::disk(config('voyager.storage.disk'))->url($file->download_link) ?: '' }}"> {{ $file->original_name ?: '' }} </a>
                @endforeach
            @else
                <a class="fileType" target="_blank" href="{{ Storage::disk(config('voyager.storage.disk'))->url($dataTypeContent->{$row->field}) }}"> {{ __('voyager::generic.download') }} </a>
            @endif
        @endif
        <input @if($row->required == 1 && !isset($dataTypeContent->{$row->field})) required @endif type="file" name="{{ $row->field }}[]" multiple="multiple">
    @overwrite
@endif

{{--  Render BREA[D] --}}

@if ($action == 'browse')
    @section("formfield_browse")
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
    @overwrite
@endif

@if ($action == 'read')
    @section("formfield_read")
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
    @overwrite
@endif

@if ($action == 'edit')
    @section("formfield_edit")
        @yield("formfield_edit_add")
    @overwrite
@endif

@if ($action == 'add')
    @section("formfield_add")
        @yield("formfield_edit_add")
    @overwrite
@endif

@yield("formfield_{$action}")
