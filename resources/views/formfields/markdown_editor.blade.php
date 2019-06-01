<textarea class="form-control simplemde" name="{{ $row->field }}" id="markdown{{ $row->field }}">{{ old($row->field, $dataTypeContent->{$row->field} ?? '') }}</textarea>
