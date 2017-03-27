@if(isset($dataTypeContent->{$row->field}))
    <br>
    <small>Leave empty to keep the same</small>
@endif
<input type="password" class="form-control" name="{{ $row->field }}" value="">