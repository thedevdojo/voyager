@if(isset($dataTypeContent->{$row->field}))
    @foreach(json_decode($dataTypeContent->{$row->field}) as $file)
        <br/><a class="fileType" href="/storage/{{ $file->download_link or '' }}"> {{ $file->original_name or '' }} </a>
    @endforeach
@endif
<input @if($row->required == 1) required @endif type="file" name="{{ $row->field }}[]" multiple="multiple">
