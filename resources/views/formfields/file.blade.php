@if(isset($dataTypeContent->{$row->field}))
    @if(json_decode($dataTypeContent->{$row->field}))
        @foreach(json_decode($dataTypeContent->{$row->field}) as $file)
            <br/><a class="fileType" href="/storage/{{ $file->download_link or '' }}"> {{ $file->original_name or '' }} </a>
        @endforeach
    @else
        <a class="fileType" href="/storage/{{ $dataTypeContent->{$row->field} }}"> Download </a>
    @endif
@endif
<input @if($row->required == 1) @endif type="file" name="{{ $row->field }}[]" multiple="multiple">
