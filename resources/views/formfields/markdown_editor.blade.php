<textarea class="form-control simplemde" name="{{ $row->field }}" id="markdown{{ $row->field }}">{{ $dataTypeContent->{$row->field} ?? old($row->field, '') }}</textarea>
