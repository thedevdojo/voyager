@if(isset($dataTypeContent->{$row->field}))
    <a class="fileType" href="/storage/{{ $dataTypeContent->{$row->field} }}"> {{ __('voyager.generic.download') }} </a>
@endif
<input @if($row->required == 1) required @endif type="file" name="{{ $row->field }}">
