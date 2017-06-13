@if(isset($dataTypeContent->{$row->field}))
    <br>
    <small>Leave empty to keep the same</small>
@endif
<input @if($row->required == 1) required @endif type="password" class="form-control" name="{{ $row->field }}" value="">